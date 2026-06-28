<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonAn;
use App\Models\NhaHang;
use App\Models\GiamGia;
use App\Models\DonHang;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $address = $request->input('address', 'Không rõ địa chỉ');
        $history = $request->input('history', []);

        if (empty($message)) {
            return response()->json([
                'success' => false,
                'message' => 'Tin nhắn không được để trống.'
            ], 400);
        }

        $messageLower = mb_strtolower($message, 'UTF-8');

        // Secure Information Interception (Intents: Orders, Personal Profile, Payments)
        $isSecureQuery = false;
        $secureTerms = [
            // Order terms
            'đơn hàng', 'đang ở đâu', 'bao giờ giao', 'hủy đơn', 'kiểm tra', 'mã đơn', 'shipper', 'tra cứu đơn', 'tìm đơn',
            // Personal info terms
            'thông tin cá nhân', 'tài khoản', 'mật khẩu', 'profile', 'số điện thoại', 'sđt', 'email', 'địa chỉ của tôi', 'hồ sơ',
            // Payment terms
            'thanh toán', 'chuyển khoản', 'ngân hàng', 'ví tiền', 'vietqr', 'payos', 'momo', 'nạp tiền', 'banking', 'số dư'
        ];
        
        foreach ($secureTerms as $term) {
            if (str_contains($messageLower, $term)) {
                $isSecureQuery = true;
                break;
            }
        }

        if ($isSecureQuery) {
            // Force Login Check first!
            if (!auth()->check()) {
                $replyMessage = "Dạ! Để bảo mật **thông tin cá nhân, đơn hàng và giao dịch thanh toán** của bạn, quý khách vui lòng **Đăng Nhập** tài khoản TAFOOD trước nhé! 🔐🔒\n\nBạn có thể click vào nút **Đăng nhập / Đăng ký** màu vàng ở góc trên bên phải màn hình để đăng nhập nhanh chóng chỉ trong vài giây. TAFOOD AI luôn sẵn sàng hỗ trợ bạn ngay sau đó ạ! 🥰";
                return response()->json([
                    'success' => true,
                    'data' => [
                        'message' => $replyMessage,
                        'recommended_foods' => []
                    ]
                ]);
            }

            $user = auth()->user();

            // User is logged in, check if query relates specifically to order check
            $isOrderQuery = false;
            $orderTerms = ['đơn hàng', 'đang ở đâu', 'bao giờ giao', 'hủy đơn', 'kiểm tra', 'mã đơn', 'shipper', 'tra cứu đơn', 'tìm đơn'];
            foreach ($orderTerms as $term) {
                if (str_contains($messageLower, $term)) {
                    $isOrderQuery = true;
                    break;
                }
            }

            if ($isOrderQuery) {
                // Try to extract order number (e.g. #15 or 15 or TF15)
                preg_match('/(?:#|TF)?(\d+)/i', $message, $matches);
                $searchedOrder = null;
                if (!empty($matches[1])) {
                    $orderId = (int)$matches[1];
                    $searchedOrder = DonHang::where('MaNguoiDung', $user->MaNguoiDung)
                        ->where('MaDonHang', $orderId)
                        ->with('chiTiet.monAn')
                        ->first();
                }

                if ($searchedOrder) {
                    $status = $searchedOrder->TrangThai;
                    $items = [];
                    foreach ($searchedOrder->chiTiet as $detail) {
                        if ($detail->monAn) {
                            $items[] = "{$detail->SoLuong}x {$detail->monAn->TenMonAn}";
                        }
                    }
                    $itemsStr = implode(", ", $items);
                    
                    $replyMessage = "🔍 **Thông tin Đơn hàng #{$searchedOrder->MaDonHang}** của bạn:\n"
                        . "• **Trạng thái:** `{$status}` 🕒\n"
                        . "• **Món ăn:** {$itemsStr}\n"
                        . "• **Tổng tiền:** " . number_format($searchedOrder->TongTien, 0, ',', '.') . "đ\n"
                        . "• **Địa chỉ:** {$searchedOrder->DiaChiGiaoHang}\n\n"
                        . "💡 Bạn có thể vào phần **Lịch sử đơn hàng** để xem bản đồ giao hàng hoặc khiếu nại nếu cần thiết nhé! 🥰\n\n[VIEW_ORDER_DETAIL_{$searchedOrder->MaDonHang}] [CHECK_OTHER_ORDERS_LINK]";
                        
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'message' => $replyMessage,
                            'recommended_foods' => []
                        ]
                    ]);
                }

                // If no specific order number was searched, return recent 3 orders
                $recentOrders = DonHang::where('MaNguoiDung', $user->MaNguoiDung)
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
                    
                if ($recentOrders->count() > 0) {
                    $orderList = [];
                    foreach ($recentOrders as $order) {
                        $items = [];
                        foreach ($order->chiTiet as $d) {
                            if ($d->monAn) {
                                $items[] = "{$d->SoLuong}x {$d->monAn->TenMonAn}";
                            }
                        }
                        $itemsStr = count($items) > 1 ? ($items[0] . " và " . (count($items) - 1) . " món khác") : ($items[0] ?? 'Không rõ món');
                        
                        $orderList[] = "• **Đơn #{$order->MaDonHang}** ({$itemsStr}): Trạng thái `{$order->TrangThai}` [TRACK_ORDER_{$order->MaDonHang}] [VIEW_ORDER_DETAIL_{$order->MaDonHang}]";
                    }
                    
                    $replyMessage = "Dạ! 🛵 Dưới đây là danh sách **các đơn hàng gần đây của bạn**:\n\n"
                        . implode("\n", $orderList)
                        . "\n\n**Quý khách muốn kiểm tra đơn hàng nào ở trên ạ?** Hãy bấm trực tiếp nút **Tra cứu** bên cạnh hoặc nhập mã đơn hàng của bạn (ví dụ: *#15*) để mình trả lời ngay nhé! 👇";
                } else {
                    $replyMessage = "Dạ! Hiện tại bạn chưa có đơn hàng nào vừa đặt trên hệ thống TAFOOD nên mình chưa thể tra cứu trạng thái. Bạn có muốn mình gợi ý vài món bán chạy nhất để đặt ngay không nè? 😍";
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'message' => $replyMessage,
                        'recommended_foods' => []
                    ]
                ]);
            }

            // If query is secure but not a specific order status query (e.g. personal info, profile, payment inquiries)

            // --- Intent: Đổi / Thay đổi mật khẩu ---
            if (str_contains($messageLower, 'mật khẩu') || str_contains($messageLower, 'đổi pass') || str_contains($messageLower, 'thay pass')) {
                $replyMessage = "Dạ! 🔑 Để **đổi mật khẩu** tài khoản TAFOOD, bạn vui lòng làm theo các bước sau:\n\n"
                    . "1️⃣ Bấm vào **ảnh đại diện / tên của bạn** ở góc trên bên phải.\n"
                    . "2️⃣ Chọn mục **Thông tin tài khoản / Hồ sơ cá nhân**.\n"
                    . "3️⃣ Cuộn xuống phần **Đổi mật khẩu**, nhập mật khẩu cũ và mật khẩu mới rồi bấm **Lưu**.\n\n"
                    . "💡 Mật khẩu mới nên có ít nhất **8 ký tự**, bao gồm chữ hoa, chữ thường và số để bảo mật tốt hơn nhé! 🔒 [GO_TO_PROFILE]";

            // --- Intent: Cập nhật số điện thoại ---
            } elseif (str_contains($messageLower, 'số điện thoại') || str_contains($messageLower, 'sđt') || str_contains($messageLower, 'phone') || str_contains($messageLower, 'điện thoại')) {
                $phone = $user->SDT ?? $user->SoDienThoai ?? null;
                $phoneStr = $phone ? "**{$phone}**" : "*(Chưa cập nhật)*";
                $replyMessage = "Dạ! 📱 Số điện thoại đang đăng ký trên tài khoản của bạn là: {$phoneStr}\n\n"
                    . "Nếu bạn muốn **thay đổi hoặc bổ sung số điện thoại**, hãy vào phần **Thông tin tài khoản** và cập nhật ngay nhé! TAFOOD sẽ dùng số này để liên hệ khi có vấn đề với đơn hàng của bạn. 🥰 [GO_TO_PROFILE]";

            // --- Intent: Xem hồ sơ / thông tin tài khoản ---
            } elseif (str_contains($messageLower, 'hồ sơ') || str_contains($messageLower, 'profile') || str_contains($messageLower, 'thông tin cá nhân') || str_contains($messageLower, 'tài khoản') || str_contains($messageLower, 'cá nhân')) {
                $displayName = $user->TenNguoiDung ?? 'Chưa cập nhật';
                $emailStr    = $user->Email ?? 'Chưa cập nhật';
                $addressStr  = $user->DiaChi ?? 'Chưa cập nhật';
                $replyMessage = "Dạ! 👤 Dưới đây là **thông tin tài khoản** của bạn trên TAFOOD:\n\n"
                    . "• **Họ và tên:** {$displayName}\n"
                    . "• **Email:** {$emailStr}\n"
                    . "• **Địa chỉ đăng ký:** {$addressStr}\n\n"
                    . "💡 Bạn có thể bấm nút bên dưới hoặc chọn **Thông tin tài khoản** ở góc phải màn hình để chỉnh sửa bất kỳ lúc nào nhé! 😍 [GO_TO_PROFILE]";

            // --- Intent: Cập nhật địa chỉ ---
            } elseif (str_contains($messageLower, 'địa chỉ') || str_contains($messageLower, 'địa chỉ của tôi')) {
                $addressStr = $user->DiaChi ?? 'Chưa cập nhật';
                $replyMessage = "Dạ! 🏠 Địa chỉ đăng ký hiện tại của bạn là: **{$addressStr}**\n\n"
                    . "Nếu bạn muốn **cập nhật địa chỉ giao hàng mặc định**, hãy vào **Thông tin tài khoản** và chỉnh sửa nhé! Địa chỉ này sẽ được dùng để điền sẵn khi bạn thanh toán cho tiện. 🛵 [GO_TO_PROFILE]";

            // --- Intent: Email ---
            } elseif (str_contains($messageLower, 'email')) {
                $emailStr = $user->Email ?? 'Chưa cập nhật';
                $replyMessage = "Dạ! 📧 Email đang đăng ký trên tài khoản TAFOOD của bạn là: **{$emailStr}**\n\n"
                    . "Email này được dùng để nhận thông báo đơn hàng và khôi phục mật khẩu. Nếu bạn muốn thay đổi, hãy vào phần **Thông tin tài khoản** nhé! 🔐 [GO_TO_PROFILE]";

            // --- Intent: Thanh toán ---
            } elseif (str_contains($messageLower, 'thanh toán') || str_contains($messageLower, 'chuyển khoản') || str_contains($messageLower, 'ngân hàng') || str_contains($messageLower, 'vietqr') || str_contains($messageLower, 'momo') || str_contains($messageLower, 'banking')) {
                $replyMessage = "Dạ! 💳 Về **giao dịch thanh toán**, hệ thống TAFOOD hỗ trợ các hình thức:\n1. 💵 **Thanh toán tiền mặt khi nhận hàng (COD).**\n2. 📲 **Chuyển khoản VietQR siêu tốc:** Hệ thống sẽ tự tạo mã QR động chứa sẵn số tiền và mã giao dịch tại bước thanh toán để bạn quét app ngân hàng thanh toán tự động, tiền sẽ được duyệt ngay lập tức nhé! 🥰";

            // --- Generic secure fallback ---
            } else {
                $displayName = $user->TenNguoiDung ?? 'Khách hàng';
                $addressStr  = $user->DiaChi ?? 'Chưa cập nhật';
                $replyMessage = "Dạ! Chào mừng **{$displayName}** đến với mục hỗ trợ tài khoản bảo mật! 🔐\n• **Họ và tên:** {$displayName}\n• **Email:** {$user->Email}\n• **Địa chỉ đăng ký:** {$addressStr}\n\n💡 Bạn có thể bấm vào phần **Thông tin tài khoản** ở góc phải màn hình để chỉnh sửa địa chỉ giao hàng hoặc cập nhật thông tin cá nhân bất kỳ lúc nào nhé! 😍 [GO_TO_PROFILE]";
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $replyMessage,
                    'recommended_foods' => []
                ]
            ]);
        }

        // 1. Fetch DB Context (active foods, select fields to save tokens)
        $activeFoods = MonAn::where('TrangThai', 'Còn bán')
            ->with('nhaHang')
            ->get()
            ->map(function ($food) {
                return [
                    'id' => $food->MaMonAn,
                    'name' => $food->TenMonAn,
                    'category' => $food->DanhMuc,
                    'price' => (float)$food->Gia,
                    'rating' => $food->diem_trung_binh,
                    'restaurant' => $food->nhaHang ? $food->nhaHang->TenNhaHang : 'Không rõ',
                    'restaurant_name' => $food->nhaHang ? $food->nhaHang->TenNhaHang : 'Không rõ',
                    'restaurant_id' => $food->MaNhaHang,
                    'description' => $food->MoTa ?? ''
                ];
            });

        $activeVouchers = GiamGia::active()
            ->get()
            ->map(function ($voucher) {
                return [
                    'code' => $voucher->MaCode,
                    'description' => $voucher->MoTa,
                    'discount_type' => $voucher->LoaiGiamGia,
                    'discount_value' => $voucher->LoaiGiamGia === 'TienMat' ? (float)$voucher->GiamToiDa : (float)$voucher->PhanTram,
                    'min_order' => (float)$voucher->DonHangToiThieu,
                ];
            });

        // 2. Fetch Gemini API Key
        $apiKey = session('GEMINI_API_KEY') ?? env('GEMINI_API_KEY');
        
        // Fallback simulation if Gemini API Key is not set up
        if (empty($apiKey)) {
            return $this->handleSimulatedResponse($message, $activeFoods, $activeVouchers, $lat, $lng);
        }

        $foodsContext = json_encode($activeFoods, JSON_UNESCAPED_UNICODE);
        $vouchersContext = json_encode($activeVouchers, JSON_UNESCAPED_UNICODE);

        $systemInstruction = "Bạn là TAFOOD AI, một Trợ lý Ẩm thực vui vẻ, hóm hỉnh, và cực kỳ chu đáo của nền tảng đặt đồ ăn trực tuyến TAFOOD tại Việt Nam.
Nhiệm vụ của bạn là trò chuyện tự nhiên, tư vấn món ăn ngon, mã giảm giá, hỗ trợ đơn hàng và giải quyết khiếu nại cho khách hàng.

Dưới đây là danh sách CÁC MÓN ĂN THẬT ĐANG BÁN trong hệ thống TAFOOD:
{$foodsContext}

Dưới đây là danh sách CÁC MÃ GIẢM GIÁ THẬT ĐANG HOẠT ĐỘNG:
{$vouchersContext}

QUY TẮC PHÂN TÍCH Ý ĐỊNH VÀ PHẢN HỒI (INTENTS & BEHAVIORS):
Bạn phải hiểu và xử lý xuất sắc các nhóm ý định sau đây của Khách hàng:

A. Tìm kiếm món ăn / Nhà hàng:
- Khi người dùng muốn tìm món hoặc thèm ăn một loại đồ ăn cụ thể, hãy kiểm tra danh sách MÓN ĂN THẬT phía trên. Nếu có món phù hợp, hãy tư vấn và đưa ID của món đó vào 'recommended_food_ids'.
- Nếu không có món đó trong danh sách, hãy giải thích lịch sự và gợi ý các món tương tự đang có sẵn trên hệ thống.

B. Hỏi về Menu / Giá cả / Phí:
- Tư vấn về giá của món ăn cụ thể trong danh sách.
- Giải thích về phí ship: Hệ thống tính phí ship tự động dựa trên khoảng cách địa lý (Haversine formula). Phí ship bình thường là khoảng 5.000đ/km, và hoàn toàn KHÔNG có phụ phí ban đêm ẩn!

C. Khuyến mãi / Voucher:
- Trình bày danh sách CÁC MÃ GIẢM GIÁ THẬT phía trên kèm theo điều kiện sử dụng (đơn tối thiểu bao nhiêu). Hướng dẫn người dùng áp dụng mã tại bước thanh toán.

D. Trạng thái đơn hàng (Hủy đơn / Kiểm tra đơn):
- Khi người dùng hỏi về đơn hàng (ví dụ: 'Đơn hàng mã #TF12345 của tôi đang ở đâu rồi?', 'hủy đơn', 'bao giờ giao', 'shipper'):
- Hãy trả lời lịch sự: 'Dạ, bạn vui lòng truy cập vào trang Cá nhân -> Lịch sử đơn hàng để theo dõi trực tiếp trạng thái chuẩn bị món và định vị tài xế thời gian thực nhé! Nếu đơn hàng vừa đặt và cửa hàng chưa làm món, bạn hoàn toàn có thể nhấn nút Hủy Đơn ngay trên giao diện đó nha! 🛵'

E. Khiếu nại / Hỗ trợ (Thiếu món, đồ ăn hỏng, hoàn tiền...):
- Khi người dùng gặp sự cố hoặc phản ánh (ví dụ: thiếu món, nguội, đổ vỡ, thái độ shipper, muốn hoàn tiền...):
- Hãy xoa dịu khách hàng bằng sự đồng cảm sâu sắc, chân thành.
- Hướng dẫn rõ ràng: 'Dạ, mình thành thật xin lỗi bạn vì trải nghiệm chưa trọn vẹn này ạ! 🥺 Bạn hãy vui lòng chụp ảnh hóa đơn/đồ ăn và gửi phản hồi ngay tại mục Khiếu Nại trong chi tiết đơn hàng đó, hoặc liên hệ trực tiếp tổng đài hotline TAFOOD 1900-xxxx (hoạt động 24/7) để chúng mình hỗ trợ hoàn tiền và xử lý cho bạn ngay lập tức nhé!'

QUY TẮC CỰC KỲ QUAN TRỌNG:
1. Bạn CHỈ ĐƯỢC PHÉP gợi ý các món ăn có trong danh sách CÁC MÓN ĂN THẬT phía trên. Tuyệt đối không tự bịa ra món ăn hay nhà hàng không tồn tại.
2. Trả lời bằng tiếng Việt tự nhiên, ấm áp, sử dụng nhiều emoji dễ thương phù hợp với chủ đề ẩm thực 🍔🍕🥤.
3. Khi bạn đề xuất/nhắc đến món ăn nào có thật, bạn phải lấy đúng ID (MaMonAn) của món đó từ danh sách trên để đưa vào trường 'recommended_food_ids' trong kết quả trả về. Tuyệt đối KHÔNG ĐƯỢC viết kèm ID dạng '(ID: X)' hay 'mã số X' vào trong câu văn phản hồi của bạn để tránh làm xấu giao diện.
4. Nếu khách hàng muốn xem menu thực đơn hoặc danh sách món ăn đầy đủ của một nhà hàng cụ thể (ví dụ: 'cho tôi xem menu của quán đó', 'xem menu Bún Chả Hà Nội tv'), bạn hãy gợi ý 3-4 món nổi bật nhất của nhà hàng đó từ danh sách, đồng thời bắt buộc ghi kèm token định dạng `[OPEN_RESTAURANT_MENU_ID]` (thay ID bằng mã nhà hàng thực tế, ví dụ `[OPEN_RESTAURANT_MENU_1]` nếu mã nhà hàng của họ là 1) ở cuối câu trả lời trong trường 'message'. Điều này giúp hệ thống tự động hiển thị nút Xem thực đơn đầy đủ bằng popup cực đẹp cho khách.
5. BẮT BUỘC trả về dữ liệu duy nhất dưới dạng một chuỗi JSON thuần có cấu trúc chính xác như sau (không bao gồm markdown ```json hay bất kỳ văn bản nào khác ngoài JSON):
{
  \"message\": \"Câu trả lời trò chuyện và gợi ý tự nhiên của bạn bằng tiếng Việt...\",
  \"recommended_food_ids\": [1, 2, 3]
}
Nếu không có gợi ý món cụ thể nào, hãy để mảng 'recommended_food_ids' rỗng [].";

        $locationContext = "";
        if (!empty($lat) && !empty($lng)) {
            $locationContext = "\n\n[BỐ CẢNH VỊ TRÍ HIỆN TẠI CỦA NGƯỜI DÙNG: Vĩ độ {$lat}, Kinh độ {$lng}, Địa chỉ: {$address}. Bạn hãy ưu tiên giới thiệu các nhà hàng/món ăn có khoảng cách địa lý gần với vị trí này nhất. Nếu họ hỏi về khoảng cách hoặc tìm quán gần đây, hãy sử dụng thông tin này để tư vấn chuyên nghiệp!]";
        }

        $historyContext = "";
        if (!empty($history) && is_array($history)) {
            $historyContext = "\n\n[LỊCH SỬ CUỘC TRÒ CHUYỆN GẦN ĐÂY ĐỂ BẠN THAM KHẢO NGỮ CẢNH:\n";
            foreach ($history as $chat) {
                if (!empty($chat['sender']) && !empty($chat['text'])) {
                    $senderName = ($chat['sender'] === 'user') ? 'Khách' : 'TAFOOD AI';
                    $historyContext .= "- {$senderName}: \"{$chat['text']}\"\n";
                }
            }
            $historyContext .= "]";
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemInstruction . $locationContext . $historyContext . "\n\nNgười dùng nói: " . $message]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $responseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Parse the JSON output from Gemini
                $data = json_decode(trim($responseText), true);
                if (json_last_error() === JSON_ERROR_NONE && isset($data['message'])) {
                    $recommendedFoods = [];
                    if (!empty($data['recommended_food_ids'])) {
                        $recommendedFoods = MonAn::whereIn('MaMonAn', $data['recommended_food_ids'])
                            ->where('TrangThai', 'Còn bán')
                            ->with('nhaHang')
                            ->get()
                            ->map(function ($food) {
                                return [
                                    'id' => $food->MaMonAn,
                                    'name' => $food->TenMonAn,
                                    'price' => (float)$food->Gia,
                                    'price_formatted' => number_format((float)$food->Gia, 0, ',', '.') . 'đ',
                                    'image' => $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png'),
                                    'rating' => $food->diem_trung_binh,
                                    'restaurant_name' => $food->nhaHang ? $food->nhaHang->TenNhaHang : 'Không rõ',
                                    'restaurant_id' => $food->MaNhaHang
                                ];
                            })->toArray();
                    }

                    // Dynamic Safeguard: If Gemini text points "down" but no recommended foods were supplied, or if the text mentions suggesting/introducing foods but the list is empty:
                    $textLower = mb_strtolower($data['message'], 'UTF-8');
                    if (empty($recommendedFoods) && 
                        (str_contains($textLower, 'dưới đây') || 
                         str_contains($textLower, 'gợi ý') || 
                         str_contains($textLower, 'đề xuất') || 
                         str_contains($textLower, '👇') || 
                         str_contains($textLower, '👉'))) {
                        
                        // Automatically recommend top 3 highly-rated active foods in system
                        $fallbackFoods = MonAn::where('TrangThai', 'Còn bán')
                            ->with('nhaHang')
                            ->orderByDesc('diem_trung_binh')
                            ->take(3)
                            ->get();
                            
                        $recommendedFoods = $fallbackFoods->map(function ($food) {
                            return [
                                'id' => $food->MaMonAn,
                                'name' => $food->TenMonAn,
                                'price' => (float)$food->Gia,
                                'price_formatted' => number_format((float)$food->Gia, 0, ',', '.') . 'đ',
                                'image' => $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png'),
                                'rating' => $food->diem_trung_binh,
                                'restaurant_name' => $food->nhaHang ? $food->nhaHang->TenNhaHang : 'Không rõ'
                            ];
                        })->toArray();
                    }

                    // If it is STILL empty (e.g. database has no foods at all), sanitize the text to not point down
                    if (empty($recommendedFoods)) {
                        $data['message'] = str_replace(
                            ['dưới đây nè! 👇', 'dưới đây nè 👇', 'dưới đây 👇', 'dưới đây! 👇', 'dưới đây', '👇', '👉'], 
                            ['', '', '', '', '', '', ''], 
                            $data['message']
                        );
                        $data['message'] = preg_replace('/\n{2,}/', "\n", $data['message']);
                    }

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'message' => $data['message'],
                            'recommended_foods' => $recommendedFoods
                        ]
                    ]);
                }
            }

            Log::error('Gemini API Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Chatbot exception: ' . $e->getMessage());
        }

        // Fallback to smart simulation if anything goes wrong
        return $this->handleSimulatedResponse($message, $activeFoods, $activeVouchers, $lat, $lng);
    }

    private function handleSimulatedResponse($message, $activeFoods, $activeVouchers, $lat = null, $lng = null)
    {
        $messageLower = mb_strtolower($message, 'UTF-8');
        $matchedIds = [];
        $replyMessage = "";

        // 1. High Priority: Location Change Request check
        $forceChooser = false;
        $forceTerms = ['chọn vị trí', 'đổi vị trí', 'thay đổi vị trí', 'nhập vị trí', 'định vị lại', 'đổi địa chỉ', 'nhập địa chỉ', 'cập nhật vị trí', 'chọn địa chỉ'];
        foreach ($forceTerms as $term) {
            if (str_contains($messageLower, $term)) {
                $forceChooser = true;
                break;
            }
        }

        // 2. High Priority: Order Status Check (Intent D)
        $isOrderQuery = false;
        $orderTerms = ['đơn hàng', 'đang ở đâu', 'bao giờ giao', 'hủy đơn', 'kiểm tra', 'mã đơn', 'shipper'];
        foreach ($orderTerms as $term) {
            if (str_contains($messageLower, $term)) {
                $isOrderQuery = true;
                break;
            }
        }
        if ($isOrderQuery) {
            $replyMessage = "Dạ! 🛵 Để **kiểm tra trạng thái đơn hàng** (ví dụ mã đơn #TFxxxx) hoặc theo dõi vị trí của **shipper**, bạn vui lòng làm theo các bước sau:\n1. Bấm vào ảnh đại diện cá nhân ở góc trên bên phải.\n2. Chọn **Lịch sử đơn hàng**.\n3. Chọn đơn hàng bạn muốn theo dõi để xem chi tiết thời gian giao đồ.\n\n❌ **Để hủy đơn hàng:** Bạn có thể nhấn nút **Hủy đơn** trực tiếp trong trang chi tiết đơn hàng đó nếu nhà hàng chưa bấm xác nhận bắt đầu làm món nhé! 🥰";
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $replyMessage,
                    'recommended_foods' => []
                ]
            ]);
        }

        // 2b. High Priority: Food Review/Rating Check
        $isReviewQuery = false;
        $reviewTerms = ['đánh giá', 'nhận xét', 'review', 'viết đánh giá', 'chấm điểm', 'sao'];
        foreach ($reviewTerms as $term) {
            if (str_contains($messageLower, $term)) {
                $isReviewQuery = true;
                break;
            }
        }
        if ($isReviewQuery) {
            $replyMessage = "Chào bạn yêu TAFOOD! 🥰 Để chia sẻ cảm nhận và **đánh giá món ăn**, bạn hãy làm theo hướng dẫn cực kỳ đơn giản sau nhé:\n\n"
                . "1️⃣ Bấm vào **ảnh đại diện / tên** ở góc trên bên phải màn hình.\n"
                . "2️⃣ Chọn mục **Lịch sử đơn hàng** 📋.\n"
                . "3️⃣ Tìm đơn hàng bạn đã thưởng thức và nhấn vào **Chi tiết**.\n"
                . "4️⃣ Chọn nút **Đánh giá** bên cạnh món ăn để chấm điểm sao và viết nhận xét của mình nhé! ⭐✨\n\n"
                . "💡 Đánh giá của bạn là động lực vô cùng to lớn giúp nhà hàng ngày càng nấu ăn ngon hơn đó ạ! Cảm ơn bạn rất nhiều! 💕";
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $replyMessage,
                    'recommended_foods' => []
                ]
            ]);
        }

        // 3. High Priority: Complaints & Support Check (Intent E)
        $isComplaintQuery = false;
        $complaintTerms = ['thiếu món', 'nguội', 'hỏng', 'đổ', 'thái độ', 'hoàn tiền', 'tổng đài', 'report', 'khiếu nại', 'hotline', '1900'];
        foreach ($complaintTerms as $term) {
            if (str_contains($messageLower, $term)) {
                $isComplaintQuery = true;
                break;
            }
        }
        if ($isComplaintQuery) {
            $replyMessage = "Dạ, TAFOOD thành thật xin lỗi bạn vì sự cố trải nghiệm chưa trọn vẹn này ạ! 🥺💔\n\nBạn hãy hoàn toàn yên tâm nha, chúng mình luôn có chính sách bảo vệ khách hàng 100%:\n1. 📸 **Gửi phản hồi:** Bạn có thể chụp ảnh đồ ăn/hóa đơn và gửi khiếu nại trực tiếp tại nút **Khiếu Nại** trong chi tiết đơn hàng đó.\n2. 📞 **Tổng đài hỗ trợ:** Hoặc bạn có thể gọi ngay Hotline tổng đài hỗ trợ **1900-xxxx** (hoạt động 24/7) để được tư vấn viên hỗ trợ đổi trả hoặc hoàn tiền lập tức nhé! Cảm ơn bạn rất nhiều vì đã góp ý!";
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $replyMessage,
                    'recommended_foods' => []
                ]
            ]);
        }

        // 4. High Priority: Menu / Price / Fees Check (Intent B)
        $isMenuOrFeeQuery = false;
        $feeTerms = ['giá', 'bao nhiêu', 'menu', 'thực đơn', 'đắt không', 'phí ship', 'giao hàng', 'phụ phí'];
        foreach ($feeTerms as $term) {
            if (str_contains($messageLower, $term)) {
                $isMenuOrFeeQuery = true;
                break;
            }
        }
        if ($isMenuOrFeeQuery) {
            // Check if they are searching for a specific restaurant or food item
            $cleanMenuQuery = str_replace(['menu', 'thực đơn', 'nhà hàng', 'quán', 'cửa hàng', 'tiệm'], '', $messageLower);
            $cleanMenuQuery = trim(preg_replace('/\s+/', ' ', $cleanMenuQuery));
            
            $hasSpecificSubject = false;
            if (mb_strlen($cleanMenuQuery, 'UTF-8') >= 2) {
                foreach ($activeFoods as $f) {
                    $foodNameNormalized = mb_strtolower($this->removeAccents($f['name']), 'UTF-8');
                    $foodCatNormalized = mb_strtolower($this->removeAccents($f['category']), 'UTF-8');
                    $foodResNormalized = mb_strtolower($this->removeAccents($f['restaurant_name']), 'UTF-8');
                    
                    $queryNormalized = mb_strtolower($this->removeAccents($cleanMenuQuery), 'UTF-8');
                    
                    // Filter generic words
                    $genericWords = ['toi', 'muon', 'xem', 'menu', 'thuc', 'don', 'cho', 'nha', 'hang', 'quan', 'tiem', 'cua', 'va', 'la', 'co', 'an'];
                    $queryWords = array_filter(explode(' ', $queryNormalized), function($w) use ($genericWords) {
                        return !in_array($w, $genericWords);
                    });
                    
                    if (empty($queryWords)) continue;
                    
                    $allMatch = true;
                    foreach ($queryWords as $word) {
                        $inName = str_contains($foodNameNormalized, $word);
                        $inCat = str_contains($foodCatNormalized, $word);
                        $inRes = str_contains($foodResNormalized, $word);
                        if (!$inName && !$inCat && !$inRes) {
                            $allMatch = false;
                            break;
                        }
                    }
                    
                    if ($allMatch) {
                        $hasSpecificSubject = true;
                        break;
                    }
                }
            }
            
            if (!$hasSpecificSubject) {
                $replyMessage = "Dạ! 🍔 Về **thực đơn (menu) và giá cả**, bạn có thể bấm trực tiếp vào bất kỳ món ăn nào đang hiển thị trên trang chủ để xem mô tả chi tiết, đánh giá và đặt món ngay nhé!\n\n... ";
                $foods = $activeFoods->sortByDesc('rating')->take(3);
                $matchedIds = $foods->pluck('id')->toArray();
                
                $recommendedFoods = [];
                if (!empty($matchedIds)) {
                    $recommendedFoods = MonAn::whereIn('MaMonAn', $matchedIds)
                        ->where('TrangThai', 'Còn bán')
                        ->with('nhaHang')
                        ->get()
                        ->map(function ($food) {
                            return [
                                'id' => $food->MaMonAn,
                                'name' => $food->TenMonAn,
                                'price' => (float)$food->Gia,
                                'price_formatted' => number_format((float)$food->Gia, 0, ',', '.') . 'đ',
                                'image' => $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png'),
                                'rating' => $food->diem_trung_binh,
                                'restaurant_name' => $food->nhaHang ? $food->nhaHang->TenNhaHang : 'Không rõ',
                                'restaurant_id' => $food->MaNhaHang
                            ];
                        })->toArray();
                }
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'message' => $replyMessage,
                        'recommended_foods' => $recommendedFoods
                    ]
                ]);
            }
        }

        // 5. High Priority: Greeting check
        $greetings = ['chào', 'hello', 'hi', 'alo', 'tư vấn', 'bắt đầu'];
        $isGreeting = false;
        foreach ($greetings as $g) {
            if (str_contains($messageLower, $g)) {
                $isGreeting = true;
                break;
            }
        }

        // 3. High Priority: Voucher check
        if (str_contains($messageLower, 'voucher') || str_contains($messageLower, 'khuyến mãi') || str_contains($messageLower, 'mã') || str_contains($messageLower, 'giam gia')) {
            if ($activeVouchers->count() > 0) {
                $voucherList = [];
                foreach ($activeVouchers as $v) {
                    $voucherList[] = "🎁 **{$v['code']}**: {$v['description']} (Đơn tối thiểu " . number_format($v['min_order'], 0, ',', '.') . "đ)";
                }
                $replyMessage = "Chào bạn! TAFOOD đang có các mã giảm giá siêu hấp dẫn hoạt động nè:\n" . implode("\n", $voucherList) . "\n\nBạn chọn món gì để mình áp mã mua ngay nhé! 😍";
            } else {
                $replyMessage = "Hiện tại hệ thống chưa có mã giảm giá mới nào, nhưng thực đơn bên dưới đang có ưu đãi cực tốt đó nha! Bạn có muốn mình gợi ý món ăn bán chạy nhất không? 🤤";
            }
        } 
        // 4. Dynamic Restaurant Search & Recommendations
        else {
            $isRestaurantQuery = false;
            $restaurantTerms = ['nhà hàng', 'quán ăn', 'tiệm ăn', 'cửa hàng', 'quán nào', 'quán gần', 'nhà hàng gần', 'gần tôi', 'gần đây', 'gần ftooi', 'xung quanh'];
            foreach ($restaurantTerms as $term) {
                if (str_contains($messageLower, $term)) {
                    $isRestaurantQuery = true;
                    break;
                }
            }

            // Bất kỳ truy vấn nào chứa tên nhà hàng cụ thể hoặc món ăn cụ thể thì KHÔNG phải là truy vấn tìm quán gần đây chung chung
            if ($isRestaurantQuery) {
                $cleanRestaurantQuery = str_replace(['nhà hàng', 'quán', 'tiệm', 'cửa hàng', 'gần tôi', 'gần đây', 'gần ftooi', 'xung quanh', 'tìm', 'xem', 'menu', 'thực đơn'], '', $messageLower);
                $cleanRestaurantQuery = trim(preg_replace('/\s+/', ' ', $cleanRestaurantQuery));
                
                if (mb_strlen($cleanRestaurantQuery, 'UTF-8') >= 2) {
                    $hasSpecificRestaurantSubject = false;
                    foreach ($activeFoods as $f) {
                        $foodNameNormalized = mb_strtolower($this->removeAccents($f['name']), 'UTF-8');
                        $foodCatNormalized = mb_strtolower($this->removeAccents($f['category']), 'UTF-8');
                        $foodResNormalized = mb_strtolower($this->removeAccents($f['restaurant_name']), 'UTF-8');
                        
                        $queryNormalized = mb_strtolower($this->removeAccents($cleanRestaurantQuery), 'UTF-8');
                        
                        $genericWords = ['toi', 'muon', 'xem', 'menu', 'thuc', 'don', 'cho', 'nha', 'hang', 'quan', 'tiem', 'cua', 'va', 'la', 'co', 'an'];
                        $queryWords = array_filter(explode(' ', $queryNormalized), function($w) use ($genericWords) {
                            return !in_array($w, $genericWords);
                        });
                        
                        if (empty($queryWords)) continue;
                        
                        $allMatch = true;
                        foreach ($queryWords as $word) {
                            $inName = str_contains($foodNameNormalized, $word);
                            $inCat = str_contains($foodCatNormalized, $word);
                            $inRes = str_contains($foodResNormalized, $word);
                            if (!$inName && !$inCat && !$inRes) {
                                $allMatch = false;
                                break;
                            }
                        }
                        
                        if ($allMatch) {
                            $hasSpecificRestaurantSubject = true;
                            break;
                        }
                    }
                    
                    if ($hasSpecificRestaurantSubject) {
                        $isRestaurantQuery = false;
                    }
                }
            }

            if ($isRestaurantQuery || $forceChooser) {
                // If coordinates are missing or forceChooser is requested, show location chooser!
                if ($forceChooser || empty($lat) || empty($lng)) {
                    $replyMessage = "Dạ, để mình quét và tìm kiếm chính xác các nhà hàng, quán ăn ngon đang mở bán **gần bạn nhất**, bạn hãy vui lòng chọn cách xác định vị trí của mình bên dưới nhé! 👇\n\n[LOCATION_CHOOSER]";
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'message' => $replyMessage,
                            'recommended_foods' => []
                        ]
                    ]);
                }

                // If coordinates are present, calculate Haversine distance!
                $latCustomer = (float)$lat;
                $lngCustomer = (float)$lng;
                
                $restaurants = NhaHang::all()->map(function ($r) use ($latCustomer, $lngCustomer) {
                    $latRes = (float)($r->latitude ?? 21.081827);
                    $lngRes = (float)($r->longitude ?? 105.842790);
                    
                    // Haversine formula
                    $earthRadius = 6371; // km
                    $latDelta = deg2rad($latRes - $latCustomer);
                    $lonDelta = deg2rad($lngRes - $lngCustomer);
                    
                    $a = sin($latDelta / 2) * sin($latDelta / 2) +
                         cos(deg2rad($latCustomer)) * cos(deg2rad($latRes)) *
                         sin($lonDelta / 2) * sin($lonDelta / 2);
                         
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                    $distance = $earthRadius * $c;
                    
                    $r->distance = round($distance, 1);
                    return $r;
                })->sortBy('distance')->take(3);

                if ($restaurants->count() > 0) {
                    $resIds = $restaurants->pluck('MaNhaHang')->toArray();
                    $foods = MonAn::whereIn('MaNhaHang', $resIds)
                        ->where('TrangThai', 'Còn bán')
                        ->orderByDesc('Gia')
                        ->take(3)
                        ->get();
                        
                    $matchedIds = $foods->pluck('MaMonAn')->toArray();
                    
                    $resList = [];
                    foreach ($restaurants as $r) {
                        $resList[] = "**{$r->TenNhaHang}** (cách bạn {$r->distance} km)";
                    }
                    
                    $replyMessage = "Dạ, dựa trên vị trí địa lý của bạn, mình đã tìm thấy các nhà hàng ngon mở bán gần bạn nhất nè:\n- " . implode("\n- ", $resList) . "\n\nDưới đây là các món ngon bán chạy nhất từ các nhà hàng này dành cho bạn chọn lựa! 😍👇\n\n[CHANGE_LOCATION_LINK]";
                } else {
                    $replyMessage = "Hiện tại hệ thống đang cập nhật danh sách nhà hàng gần bạn. Bạn có muốn thử các món ăn nổi tiếng cực ngon dưới đây trước không nè? 🤤👇\n\n[CHANGE_LOCATION_LINK]";
                }
            } 
            // 5. Dynamic Search Engine for foods
            else {
                // Clean common stop words to isolate the subject
                $multiWordStopWords = [
                    'muốn ăn', 'muốn mua', 'tìm kiếm', 'tìm món ăn', 'tìm món',
                    'xem món ăn', 'xem món', 'món ăn', 'cho xem', 'cho hỏi', 'hỏi về',
                    'cho tôi', 'cho mình', 'địa chỉ', 'nhà hàng', 'quán ăn', 'cửa hàng',
                    'gần ftooi', 'gần tôi', 'gần đây'
                ];
                $cleanQuery = str_replace($multiWordStopWords, '', $messageLower);
                
                $singleWordStopWords = [
                    'gợi ý', 'chatbot', 'tafood', 'gần', 'không', 'quán', 'tiệm', 'thèm', 'muốn', 
                    'tìm', 'xem', 'đặt', 'mua', 'bán', 'bạn', 'nhỉ', 'nhé', 'giúp', 'mình', 
                    'được', 'nào', 'món', 'tôi', 'cho', 'với', 'cần', 'hỏi', 'ơi', 'chỉ', 'có', 'ai',
                    'menu', 'thực đơn'
                ];
                
                // Explode by space and filter exact matching single-word stop words (avoids partial matching like "bán" matching "bánh")
                $words = explode(' ', $cleanQuery);
                $filteredWords = array_filter($words, function($word) use ($singleWordStopWords) {
                    $cleanWord = trim($word, "?.,!");
                    return !in_array($cleanWord, $singleWordStopWords);
                });
                $cleanQuery = implode(' ', $filteredWords);
                
                // Clean extra spaces
                $cleanQuery = preg_replace('/\s+/', ' ', $cleanQuery);
                $cleanQuery = trim($cleanQuery);

                // Check if user is referring to "that restaurant" contextually in Simulated/Offline mode
                $contextRestaurantName = null;
                $isReferencingPreviousRestaurant = false;
                
                $refTerms = ['cửa hàng đó', 'nhà hàng đó', 'quán đó', 'tiệm đó', 'nơi đó', 'ở đó', 'của nó'];
                $menuTerms = ['menu', 'thực đơn', 'danh sách món', 'món ăn'];
                
                $hasRef = false;
                foreach ($refTerms as $ref) {
                    if (str_contains($messageLower, $ref)) {
                        $hasRef = true;
                        break;
                    }
                }
                $hasMenu = false;
                foreach ($menuTerms as $menu) {
                    if (str_contains($messageLower, $menu)) {
                        $hasMenu = true;
                        break;
                    }
                }
                
                if ($hasRef && $hasMenu) {
                    if (!empty($history) && is_array($history)) {
                        for ($i = count($history) - 1; $i >= 0; $i--) {
                            $item = $history[$i];
                            if ($item['sender'] === 'ai' && !empty($item['foods']) && is_array($item['foods'])) {
                                foreach ($item['foods'] as $f) {
                                    if (!empty($f['restaurant_name'])) {
                                        $contextRestaurantName = $f['restaurant_name'];
                                        $isReferencingPreviousRestaurant = true;
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ($isReferencingPreviousRestaurant && !empty($contextRestaurantName)) {
                    $cleanQuery = $contextRestaurantName;
                }

                if ($isGreeting || empty($cleanQuery) || mb_strlen($cleanQuery, 'UTF-8') < 2) {
                    // Return default greeting with top-rated items
                    $foods = $activeFoods->sortByDesc('rating')->take(3);
                    $matchedIds = $foods->pluck('id')->toArray();
                    $replyMessage = "Chào bạn! Mình là Trợ lý Ẩm thực TAFOOD AI 🤖. Mình có thể giúp bạn tìm các món ngon, định vị các quán bán chạy nhất, hoặc săn mã giảm giá hot giúp bạn.\n\nGợi ý ngay cho bạn 3 món ăn nổi tiếng đang được đánh giá rất cao dưới đây nè! 👇";
                } else {
                    // Search active foods dynamically using smart accent-insensitive all-word matching!
                    $foods = $activeFoods->filter(function($f) use ($cleanQuery) {
                        $foodNameNormalized = mb_strtolower($this->removeAccents($f['name']), 'UTF-8');
                        $foodCatNormalized = mb_strtolower($this->removeAccents($f['category']), 'UTF-8');
                        $foodResNormalized = mb_strtolower($this->removeAccents($f['restaurant_name']), 'UTF-8');
                        
                        $queryNormalized = mb_strtolower($this->removeAccents($cleanQuery), 'UTF-8');
                        
                        // Handle interchangeable 'my' / 'mi' in normalized space too
                        $queryNormalized = str_replace('my', 'mi', $queryNormalized);
                        $foodNameNormalized = str_replace('my', 'mi', $foodNameNormalized);
                        $foodCatNormalized = str_replace('my', 'mi', $foodCatNormalized);
                        $foodResNormalized = str_replace('my', 'mi', $foodResNormalized);
                        
                        $queryWords = array_filter(explode(' ', $queryNormalized));
                        if (empty($queryWords)) return false;
                        
                        foreach ($queryWords as $word) {
                            $inName = str_contains($foodNameNormalized, $word);
                            $inCat = str_contains($foodCatNormalized, $word);
                            $inRes = str_contains($foodResNormalized, $word);
                            if (!$inName && !$inCat && !$inRes) {
                                return false;
                            }
                        }
                        return true;
                    })->take(3);

                    if ($foods->count() > 0) {
                        $matchedIds = $foods->pluck('id')->toArray();
                        if ($isReferencingPreviousRestaurant) {
                            $replyMessage = "Dạ! Dựa theo lịch sử trò chuyện của chúng mình, đây là thực đơn các món ngon cực đắt khách của **\"" . $contextRestaurantName . "\"** dành riêng cho bạn nè! 😍👇";
                        } else {
                            $replyMessage = "Dạ, hệ thống TAFOOD đang bán những món ngon liên quan đến **\"" . $cleanQuery . "\"** cực hấp dẫn dưới đây nè! Bạn xem chọn món ưng ý nhé! 😍👇";
                        }

                        // Smart menu token: Check if all matched foods belong to the exact same restaurant
                        $allSameRes = true;
                        $firstResId = null;
                        foreach ($foods as $f) {
                            $fResId = $f['restaurant_id'] ?? $f->MaNhaHang ?? null;
                            if ($firstResId === null) {
                                $firstResId = $fResId;
                            } elseif ($firstResId !== $fResId) {
                                $allSameRes = false;
                                break;
                            }
                        }
                        if ($allSameRes && $firstResId) {
                            $replyMessage .= " [OPEN_RESTAURANT_MENU_{$firstResId}]";
                        }
                    } else {
                        // Fallback to top-rated items but explain that food wasn't found
                        $foods = $activeFoods->sortByDesc('rating')->take(3);
                        $matchedIds = $foods->pluck('id')->toArray();
                        $replyMessage = "Tiếc quá, hiện tại hệ thống chưa bán món liên quan đến **\"" . $cleanQuery . "\"**. Bạn có muốn dùng thử những món ngon nổi tiếng cực hot đang được yêu thích dưới đây không nè? 🥺👇";
                    }
                }
            }
        }

        // Package matching foods
        $recommendedFoods = [];
        if (!empty($matchedIds)) {
            $recommendedFoods = MonAn::whereIn('MaMonAn', $matchedIds)
                ->where('TrangThai', 'Còn bán')
                ->with('nhaHang')
                ->get()
                ->map(function ($food) {
                    return [
                        'id' => $food->MaMonAn,
                        'name' => $food->TenMonAn,
                        'price' => (float)$food->Gia,
                        'price_formatted' => number_format((float)$food->Gia, 0, ',', '.') . 'đ',
                        'image' => $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png'),
                        'rating' => $food->diem_trung_binh,
                        'restaurant_name' => $food->nhaHang ? $food->nhaHang->TenNhaHang : 'Không rõ',
                        'restaurant_id' => $food->MaNhaHang
                    ];
                });
        }

        if (empty(session('GEMINI_API_KEY')) && empty(env('GEMINI_API_KEY'))) {
            $replyMessage .= "\n\n*(💡 Mẹo: Bạn hãy bấm biểu tượng chìa khóa 🔑 ở góc trên khung chat để cấu hình `GEMINI_API_KEY` miễn phí, mở khóa cuộc hội thoại tự do hoàn toàn thông minh với AI Gemini nhé!)*";
        }

        if (empty($recommendedFoods) || count($recommendedFoods) === 0) {
            $replyMessage = str_replace(
                ['dưới đây nè! 👇', 'dưới đây nè 👇', 'dưới đây 👇', 'dưới đây! 👇', 'dưới đây', '👇', '👉'], 
                ['', '', '', '', '', '', ''], 
                $replyMessage
            );
            $replyMessage = preg_replace('/\n{2,}/', "\n", $replyMessage);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $replyMessage,
                'recommended_foods' => $recommendedFoods
            ]
        ]);
    }

    public function saveApiKey(Request $request)
    {
        $key = $request->input('api_key');
        if (empty($key)) {
            session()->forget('GEMINI_API_KEY');
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa API Key thành công. Hệ thống sẽ quay lại chế độ giả lập offline.'
            ]);
        }

        session(['GEMINI_API_KEY' => $key]);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu cấu hình API Key thành công! TAFOOD AI đã được kích hoạt chế độ siêu thông minh!'
        ]);
    }

    private function removeAccents($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }
}
