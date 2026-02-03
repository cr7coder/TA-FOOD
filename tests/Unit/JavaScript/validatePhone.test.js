/**
 * Jest test file for validatePhone function
 * Run with: npm test
 */

// Mock implementation of validatePhone function from checkout-validator.js
function validatePhone(phoneNumber) {
    if (!phoneNumber.trim())
        return "Vui lòng điền số điện thoại người nhận hàng.";
    if (!/^\d+$/.test(phoneNumber) || parseInt(phoneNumber) <= 0)
        return "Vui lòng điền số điện thoại là số nguyên.";
    if (phoneNumber.length < 10 || phoneNumber.length > 11)
        return "Vui lòng điền số điện thoại có 10-11 chữ số.";
    return "";
}

describe("validatePhone Function Tests", () => {
    // Test thành công
    test("Test_ValidPhoneNumber_Should_ReturnEmpty", () => {
        const validPhones = [
            "0912345678", // 10 chữ số
            "0987654321", // 10 chữ số
            "84912345678", // 11 chữ số
            "84876543210", // 11 chữ số
        ];

        validPhones.forEach((phone) => {
            const result = validatePhone(phone);
            expect(result).toBe("");
        });
    });
    test("Test_EmptyPhoneNumber_Should_ReturnEmptyError", () => {
        const emptyPhones = ["", "   ", "\t\n"];
        emptyPhones.forEach((phone) => {
            const result = validatePhone(phone);
            expect(result).toBe("Vui lòng điền số điện thoại người nhận hàng.");
        });
    });
    test("Test_InvalidFormatPhoneNumber_Should_ReturnFormatError", () => {
        const invalidPhones = [
            "abc1234567",
            "091-234-5678",
            "091.234.5678",
            "091 234 5678",
            "+84912345678",
            "091abc45678",
        ];
        // Act & Assert
        invalidPhones.forEach((phone) => {
            const result = validatePhone(phone);
            expect(result).toBe(
                "Vui lòng điền số điện thoại là số nguyên."
            );
        });
    });

    test("Test_InvalidLengthPhoneNumber_Should_ReturnLengthError", () => {
        // Arrange
        const invalidLengthPhones = [
            "091234567", // 9 chữ số
            "09123456789012", // 14 chữ số
            "0123", // 4 chữ số
            "123456789012345", // 15 chữ số
        ];

        // Act & Assert
        invalidLengthPhones.forEach((phone) => {
            const result = validatePhone(phone);
            expect(result).toBe("Vui lòng điền số điện thoại có 10-11 chữ số.");
        });
    });
});
