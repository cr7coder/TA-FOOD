/**
 * Jest test file for validatePhanTram function
 * Run with: npm test
 */

// Mock implementation
let mockAlerts = [];

function showFieldError(name, message) {
    mockAlerts.push({ name, type: "danger", message });
}

function qByName(name) {
    return document.querySelector(`[name="${name}"]`);
}

function validatePhanTram() {
    const name = "PhanTram";
    const el = qByName(name);
    const raw = (el?.value || "").trim();
    if (!raw) {
        showFieldError(name, "Phần trăm giảm không được bỏ trống");
        return false;
    } else if (!/^-?\d+$/.test(raw)) {
        showFieldError(name, "Phần trăm giảm chỉ chấp nhận số nguyên");
        return false;
    } else {
        const v = parseInt(raw, 10);
        if (v < 1) {
            showFieldError(name, "Phần trăm giảm phải lớn hơn 0");
            return false;
        } else if (v > 100) {
            showFieldError(name, "Phần trăm giảm tối đa là 100");
            return false;
        } else {
            return true;
        }
    }
}

describe("validatePhanTram Function Tests", () => {
    beforeEach(() => {
        mockAlerts = [];
        document.body.innerHTML = '<input name="PhanTram" value="" />';
    });
    // Test thành công
    test("Test_ValidPhanTram_Should_ReturnTrue", () => {
        const input = document.querySelector('[name="PhanTram"]');
        input.value = "50";

        const result = validatePhanTram();
        expect(result).toBe(true);
        expect(mockAlerts).toHaveLength(0);
    });
    test("Test_EmptyPhanTram_Should_ReturnFalse_And_ShowEmptyAlert", () => {
        const input = document.querySelector('[name="PhanTram"]');
        input.value = "";

        const result = validatePhanTram();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Phần trăm giảm không được bỏ trống");
    });
    test("Test_NonIntegerPhanTram_Should_ReturnFalse_And_ShowFormatAlert", () => {
        const input = document.querySelector('[name="PhanTram"]');
        input.value = "50.5";

        const result = validatePhanTram();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Phần trăm giảm chỉ chấp nhận số nguyên");
    });
    test("Test_ZeroOrNegativePhanTram_Should_ReturnFalse_And_ShowRangeAlert", () => {
        const input = document.querySelector('[name="PhanTram"]');
        input.value = "0";

        const result = validatePhanTram();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Phần trăm giảm phải lớn hơn 0");
    });

    // Test lỗi 4: Quá 100%
    test("Test_OverMaxPhanTram_Should_ReturnFalse_And_ShowMaxAlert", () => {
        const input = document.querySelector('[name="PhanTram"]');
        input.value = "150";

        const result = validatePhanTram();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Phần trăm giảm tối đa là 100");
    });

    // // Test edge case: 1%
    // test("Test_MinValidPhanTram_Should_ReturnTrue", () => {
    //     // Arrange
    //     const input = document.querySelector('[name="PhanTram"]');
    //     input.value = "1";

    //     // Act
    //     const result = validatePhanTram();

    //     // Assert
    //     expect(result).toBe(true);
    //     expect(mockAlerts).toHaveLength(0);
    // });

    // // Test edge case: 100%
    // test("Test_MaxValidPhanTram_Should_ReturnTrue", () => {
    //     // Arrange
    //     const input = document.querySelector('[name="PhanTram"]');
    //     input.value = "100";

    //     // Act
    //     const result = validatePhanTram();

    //     // Assert
    //     expect(result).toBe(true);
    //     expect(mockAlerts).toHaveLength(0);
    // });
});