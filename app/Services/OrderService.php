<?php

namespace App\Services;

use App\Models\GiamGia;
use App\Models\MonAn;

class OrderService
{
    /**
     * Validate customer name
     */
    public function validateCustomerName($name)
    {
        $name = trim($name ?? '');
        
        if (empty($name)) {
            return [
                'success' => false,
                'message' => 'Họ tên người nhận hàng không được để trống (7E.4)'
            ];
        }
        
        if (strlen($name) <= 10) {
            return [
                'success' => false,
                'message' => 'Vui lòng điền họ tên lớn hơn 10 ký tự (7E.5)'
            ];
        }
        
        if (strlen($name) >= 100) {
            return [
                'success' => false,
                'message' => 'Vui lòng điền họ tên nhỏ hơn 100 ký tự (7E.6)'
            ];
        }
        
        // Chỉ cho phép chữ cái và khoảng trắng (bao gồm tiếng Việt có dấu)
        if (!preg_match('/^[a-zA-ZÀ-ỹ\s]+$/u', $name)) {
            return [
                'success' => false,
                'message' => 'Họ tên chỉ có chữ cái (7E.7)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate phone number
     */
    public function validatePhone($phone)
    {
        $phone = trim($phone ?? '');
        
        if (empty($phone)) {
            return [
                'success' => false,
                'message' => 'Vui lòng điền số điện thoại người nhận hàng (7E.8)'
            ];
        }
        
        // Kiểm tra chỉ chứa số
        if (!preg_match('/^\d+$/', $phone)) {
            return [
                'success' => false,
                'message' => 'Vui lòng điền số điện thoại là số nguyên dương (7E.9)'
            ];
        }
        
        // Kiểm tra độ dài 10-11 số
        $length = strlen($phone);
        if ($length < 10 || $length > 11) {
            return [
                'success' => false,
                'message' => 'Vui lòng điền số điện thoại có 10-11 chữ số (7E.10)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate address
     */
    public function validateAddress($address)
    {
        $address = trim($address ?? '');
        
        if (empty($address)) {
            return [
                'success' => false,
                'message' => 'Vui lòng điền địa chỉ giao hàng (7E.11)'
            ];
        }
        
        if (strlen($address) <= 10) {
            return [
                'success' => false,
                'message' => 'Vui lòng điền địa chỉ giao hàng nhiều hơn 10 ký tự (7E.12)'
            ];
        }
        
        if (strlen($address) >= 200) {
            return [
                'success' => false,
                'message' => 'Vui lòng điền địa chỉ giao hàng ít hơn 200 ký tự (7E.13)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate menu availability
     */
    public function validateMenuAvailability($maMonAn)
    {
        $monAn = MonAn::find($maMonAn);
        
        if (!$monAn || $monAn->TrangThai !== 'Còn bán') {
            return [
                'success' => false,
                'message' => 'Món ăn không có sẵn (7E.1)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate quantity
     */
    public function validateQuantity($quantity)
    {
        // Check for empty string or null first
        if ($quantity === '' || $quantity === null) {
            return [
                'success' => false,
                'message' => 'Số lượng món phải là số nguyên dương (7E.2)'
            ];
        }
        
        // If not numeric at all, return 7E.2
        if (!is_numeric($quantity)) {
            return [
                'success' => false,
                'message' => 'Số lượng món phải là số nguyên dương (7E.2)'
            ];
        }
        
        // Check if it's a string with decimal point or is float or decimal value
        if ((is_string($quantity) && strpos($quantity, '.') !== false) || 
            is_float($quantity) || 
            floatval($quantity) != intval($quantity)) {
            return [
                'success' => false,
                'message' => 'Số lượng món phải là số nguyên dương (7E.2)'
            ];
        }
        
        $qty = intval($quantity);
        
        // Check if negative (7E.2 - not positive integer)
        if ($qty < 0) {
            return [
                'success' => false,
                'message' => 'Số lượng món phải là số nguyên dương (7E.2)'
            ];
        }
        
        // Check if zero (7E.3 - must be greater than 0)
        if ($qty === 0) {
            return [
                'success' => false,
                'message' => 'Số lượng món phải lớn hơn 0 (7E.3)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate order items
     */
    public function validateOrderItems($foods)
    {
        if (empty($foods) || !is_array($foods)) {
            return [
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất 1 món ăn để đặt hàng (7E.14)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate payment method
     */
    public function validatePaymentMethod($method)
    {
        $method = trim($method ?? '');
        
        if (empty($method)) {
            return [
                'success' => false,
                'message' => 'Vui lòng chọn phương thức thanh toán (7E.15)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Process payment
     */
    public function processPayment($orderData)
    {
        // Simulate payment failure if flag is set
        if (isset($orderData['simulate_payment_failure']) && $orderData['simulate_payment_failure']) {
            return [
                'success' => false,
                'message' => 'Thanh toán không thành công, vui lòng thử lại (7E.16)'
            ];
        }
        
        // Cash payment always succeeds
        if ($orderData['PhuongThucThanhToan'] === 'Tiền mặt') {
            return ['success' => true];
        }
        
        // Other payment methods - simulate success
        return ['success' => true];
    }
    
    /**
     * Validate voucher format
     */
    public function validateVoucherFormat($voucherCode)
    {
        if (strlen($voucherCode) > 12) {
            return [
                'success' => false,
                'message' => 'Mã voucher không được vượt quá 12 ký tự (7E.19)'
            ];
        }
        
        // Chỉ cho phép chữ cái và số
        if (!preg_match('/^[A-Za-z0-9]+$/', $voucherCode)) {
            return [
                'success' => false,
                'message' => 'Mã voucher chỉ được chứa chữ cái và số (7E.18)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate voucher
     */
    public function validateVoucher($voucherCode)
    {
        // First check format
        $formatCheck = $this->validateVoucherFormat($voucherCode);
        if (!$formatCheck['success']) {
            return $formatCheck;
        }
        
        // Check if voucher exists
        $voucher = GiamGia::where('MaCode', $voucherCode)->first();
        
        if (!$voucher) {
            return [
                'success' => false,
                'message' => 'Mã voucher không tồn tại (7E.17)'
            ];
        }
        
        return ['success' => true, 'voucher' => $voucher];
    }
    
    /**
     * Validate full order data
     */
    public function validateOrderData($orderData)
    {
        // Check if foods array exists and has items
        if (!isset($orderData['foods']) || empty($orderData['foods'])) {
            return [
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất 1 món ăn để đặt hàng (7E.14)'
            ];
        }
        
        // Validate each food item availability
        foreach ($orderData['foods'] as $foodItem) {
            $maMonAn = $foodItem['MaMonAn'] ?? null;
            if (!$maMonAn) continue;
            
            $monAn = MonAn::find($maMonAn);
            if (!$monAn || $monAn->TrangThai !== 'Còn bán') {
                return [
                    'success' => false,
                    'message' => 'Thực đơn hiện không khả dụng (7E.1)'
                ];
            }
        }
        
        return ['success' => true];
    }
    
    /**
     * Apply voucher discount to order total
     */
    public function applyVoucherDiscount($orderTotal, $voucherCode)
    {
        // Validate voucher first
        $voucherCheck = $this->validateVoucher($voucherCode);
        if (!$voucherCheck['success']) {
            return $voucherCheck;
        }
        
        $voucher = $voucherCheck['voucher'];
        
        // Calculate discount
        $discount = ($orderTotal * $voucher->PhanTram) / 100;
        $finalTotal = $orderTotal - $discount;
        
        return [
            'success' => true,
            'original_total' => $orderTotal,
            'discount_amount' => $discount,
            'final_total' => $finalTotal,
            'discount_percent' => $voucher->PhanTram,
            'voucher' => $voucher
        ];
    }
}
