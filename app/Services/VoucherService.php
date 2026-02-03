<?php

namespace App\Services;

use App\Models\GiamGia;
use Carbon\Carbon;

class VoucherService
{
    /**
     * Validate voucher code
     */
    public function validateVoucherCode($code)
    {
        $code = trim($code ?? '');
        
        if (empty($code)) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không được để trống (8E.1)'
            ];
        }
        
        if (strlen($code) > 12) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không được vượt quá 12 ký tự (8E.2)'
            ];
        }
        
        // Chỉ cho phép chữ cái và số
        if (!preg_match('/^[A-Za-z0-9]+$/', $code)) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá chỉ được chứa chữ cái và số (8E.3)'
            ];
        }
        
        // Check if code already exists
        if (GiamGia::where('MaCode', $code)->exists()) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá đã tồn tại (8E.4)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate percent value
     */
    public function validatePercentValue($percent)
    {
        if ($percent === null || $percent === '') {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm không được bỏ trống (8E.5)'
            ];
        }
        
        // Check if string is not numeric before converting
        if (is_string($percent) && !is_numeric($percent)) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm không đúng định dạng (8E.8)'
            ];
        }
        
        // Check for decimal point in string (e.g., "50.5" or "10.123")
        if (is_string($percent) && strpos($percent, '.') !== false) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm không đúng định dạng (8E.8)'
            ];
        }
        
        // Convert to float if string
        $value = is_string($percent) ? (float)$percent : $percent;
        
        if ($value <= 0) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm phải lớn hơn 0 (8E.6)'
            ];
        }
        
        // Check if value is too large (e.g., 150.5)
        if ($value > 100) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm tối đa là 100 (8E.7)'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate start date
     */
    public function validateStartDate($startDate)
    {
        if (empty($startDate)) {
            return [
                'success' => false,
                'message' => 'Ngày bắt đầu không được để trống (8E.9)'
            ];
        }
        
        // Check format before parsing
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            return [
                'success' => false,
                'message' => 'Ngày bắt đầu không hợp lệ (8E.10)'
            ];
        }
        
        // Validate date format and ensure it's a valid date
        try {
            $date = Carbon::createFromFormat('Y-m-d', $startDate);
            // Check if parsed date matches input (strict validation)
            if ($date->format('Y-m-d') !== $startDate) {
                return [
                    'success' => false,
                    'message' => 'Ngày bắt đầu không hợp lệ (8E.10)'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ngày bắt đầu không hợp lệ (8E.10)'
            ];
        }
        
        // Must be today or future
        if ($date->startOfDay()->lt(Carbon::today())) {
            return [
                'success' => false,
                'message' => 'Ngày bắt đầu phải lớn hơn hoặc bằng ngày hiện tại (8E.11)'
            ];
        }
        
        return ['success' => true, 'date' => $date];
    }
    
    /**
     * Validate end date
     */
    public function validateEndDate($endDate, $startDate = null)
    {
        if (empty($endDate)) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc không được để trống (8E.12)'
            ];
        }
        
        // Check format before parsing
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc không hợp lệ (8E.13)'
            ];
        }
        
        // Validate date format and ensure it's a valid date
        try {
            $date = Carbon::createFromFormat('Y-m-d', $endDate);
            // Check if parsed date matches input (strict validation)
            if ($date->format('Y-m-d') !== $endDate) {
                return [
                    'success' => false,
                    'message' => 'Ngày kết thúc không hợp lệ (8E.13)'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc không hợp lệ (8E.13)'
            ];
        }
        
        // If start date provided, end date must be >= start date
        if ($startDate) {
            try {
                $start = Carbon::parse($startDate);
                if ($date->lt($start)) {
                    return [
                        'success' => false,
                        'message' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu (8E.14)'
                    ];
                }
            } catch (\Exception $e) {
                // Start date invalid, will be caught by validateStartDate
            }
        }
        
        return ['success' => true, 'date' => $date];
    }
    
    /**
     * Validate full voucher data
     */
    public function validateVoucherData($data)
    {
        // Validate voucher code
        if (empty($data['MaCode'])) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không được bỏ trống (8E.1)'
            ];
        }
        
        if (strlen($data['MaCode']) > 12) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không quá 12 ký tự (8E.2)'
            ];
        }
        
        if (!preg_match('/^[A-Za-z0-9]+$/', $data['MaCode'])) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không chứa ký tự đặc biệt (8E.3)'
            ];
        }
        
        if (GiamGia::where('MaCode', $data['MaCode'])->exists()) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá đã tồn tại (8E.4)'
            ];
        }
        
        // Validate percent value
        if (!isset($data['PhanTram']) || $data['PhanTram'] === '' || $data['PhanTram'] === null) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm không được bỏ trống (8E.5)'
            ];
        }
        
        // Check if value is non-numeric string
        if (is_string($data['PhanTram']) && !is_numeric($data['PhanTram'])) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm không đúng định dạng (8E.8)'
            ];
        }
        
        // Check for decimal point in string (covers '10.123', '1000.00', '10.1234')
        if (is_string($data['PhanTram']) && strpos($data['PhanTram'], '.') !== false) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm không đúng định dạng (8E.8)'
            ];
        }
        
        $percent = is_string($data['PhanTram']) ? (float)$data['PhanTram'] : $data['PhanTram'];
        
        if ($percent <= 0) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm phải lớn hơn 0 (8E.6)'
            ];
        }
        
        if ($percent > 100) {
            return [
                'success' => false,
                'message' => 'Phần trăm giảm tối đa là 100 (8E.7)'
            ];
        }
        
        // Check decimal places (max 2)
        $percentStr = (string)$percent;
        if (strpos($percentStr, '.') !== false) {
            $parts = explode('.', $percentStr);
            $decimalPart = rtrim($parts[1], '0');
            if (strlen($decimalPart) > 2) {
                return [
                    'success' => false,
                    'message' => 'Phần trăm giảm không đúng định dạng (8E.8)'
                ];
            }
        }
        
        // Validate start date
        if (empty($data['NgayBatDau'])) {
            return [
                'success' => false,
                'message' => 'Ngày bắt đầu không được bỏ trống (8E.9)'
            ];
        }
        
        // Check format before parsing
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['NgayBatDau'])) {
            return [
                'success' => false,
                'message' => 'Ngày bắt đầu không hợp lệ (8E.10)'
            ];
        }
        
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $data['NgayBatDau']);
            // Strict validation: check if parsed date matches input
            if ($startDate->format('Y-m-d') !== $data['NgayBatDau']) {
                return [
                    'success' => false,
                    'message' => 'Ngày bắt đầu không hợp lệ (8E.10)'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ngày bắt đầu không hợp lệ (8E.10)'
            ];
        }
        
        if ($startDate->startOfDay()->lt(Carbon::today())) {
            return [
                'success' => false,
                'message' => 'Ngày bắt đầu phải lớn hơn hoặc bằng ngày hiện tại (8E.11)'
            ];
        }
        
        // Validate end date
        if (empty($data['NgayKetThuc'])) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc không được bỏ trống (8E.12)'
            ];
        }
        
        // Check format before parsing
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['NgayKetThuc'])) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc không hợp lệ (8E.13)'
            ];
        }
        
        try {
            $endDate = Carbon::createFromFormat('Y-m-d', $data['NgayKetThuc']);
            // Strict validation: check if parsed date matches input
            if ($endDate->format('Y-m-d') !== $data['NgayKetThuc']) {
                return [
                    'success' => false,
                    'message' => 'Ngày kết thúc không hợp lệ (8E.13)'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc không hợp lệ (8E.13)'
            ];
        }
        
        if ($endDate->lt($startDate)) {
            return [
                'success' => false,
                'message' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu (8E.14)'
            ];
        }
        
        return ['success' => true];
    }
}
