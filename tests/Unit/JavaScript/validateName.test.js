/**
 * Jest test file for validateName function
 * Run with: npm test
 */

// Mock implementation of validateName function from checkout-validator.js
function validateName(name) {
    if (!name.trim())
        return "Họ tên người nhận hàng không được để trống.";
    if (name.length < 10)
        return "Vui lòng điền họ tên lớn hơn 10 ký tự.";
    if (name.length > 100)
        return "Vui lòng điền họ tên nhỏ hơn 100 ký tự.";
    if (!/^[a-zA-ZÀ-ỹ\s]+$/.test(name))
        return "Họ tên chỉ có chữ cái.";
    return "";
}

describe("validateName Function Tests", () => {
    // Test thành công
    test("Test_ValidCustomerName_Should_ReturnEmpty", () => {
        const validNames = [
            "Nguyễn Văn An", "Trần Thị Bình An", "Lê Hoàng Nam Khánh", "Phạm Thị Thu Hương",
        ];
        validNames.forEach((name) => {
            const result = validateName(name);
            expect(result).toBe("");
        });
    });
    test("Test_EmptyCustomerName_Should_ReturnEmptyError", () => {
        const emptyNames = ["", "   ", "\t\n"];
        emptyNames.forEach((name) => {
            const result = validateName(name);
            expect(result).toBe("Họ tên người nhận hàng không được để trống.");
        });
    });
    test("Test_TooShortCustomerName_Should_ReturnTooShortError", () => {
        const shortNames = ["A", "Abc", "Nguyễn A", "Test Name"];
        shortNames.forEach((name) => {
            const result = validateName(name);
            expect(result).toBe("Vui lòng điền họ tên lớn hơn 10 ký tự.");
        });
    });
    test("Test_TooLongCustomerName_Should_ReturnTooLongError", () => {
        const longName = "A".repeat(101);
        const result = validateName(longName);
        expect(result).toBe("Vui lòng điền họ tên nhỏ hơn 100 ký tự.");
    });
    test("Test_InvalidCharacterCustomerName_Should_ReturnInvalidCharError", () => {
        const invalidNames = [
            "Nguyễn Văn An123456",
            "John Smith Test 2024",
            "Trần Thị Bảo An@2024",
            "Test Name Number #12345",
        ];
        invalidNames.forEach((name) => {
            const result = validateName(name);
            expect(result).toBe("Họ tên chỉ có chữ cái.");
        });
    });
});
