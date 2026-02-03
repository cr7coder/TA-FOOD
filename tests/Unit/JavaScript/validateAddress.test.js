/**
 * Jest test file for validateAddress function
 * Run with: npm test
 */

// Mock implementation of validateAddress function from checkout-validator.js
function validateAddress(address) {
    if (!address.trim())
        return "Vui lòng điền địa chỉ giao hàng.";
    if (address.length < 10)
        return "Vui lòng điền địa chỉ giao hàng nhiều hơn 10 ký tự.";
    if (address.length > 200)
        return "Vui lòng điền địa chỉ giao hàng ít hơn 200 ký tự.";
    return "";
}

describe("validateAddress Function Tests", () => {
    // Test thành công
    test("Test_ValidAddress_Should_ReturnEmpty", () => {
        const validAddresses = [
            "123 Lê Lợi, Q1",
            "456 Nguyễn Huệ, Phường 1, Quận 1, TP.HCM",
            "Số 10 Đường ABC, Phường XYZ, Quận 123, Thành phố DEF",
            "Tầng 5, Tòa nhà ABC, 123 Đường XYZ, Phường 1, Quận 1, TP.HCM",
        ];
        validAddresses.forEach((address) => {
            const result = validateAddress(address);
            expect(result).toBe("");
        });
    });
    test("Test_EmptyAddress_Should_ReturnEmptyError", () => {
        const emptyAddresses = ["", "   ", "\t\n"];
        emptyAddresses.forEach((address) => {
            const result = validateAddress(address);
            expect(result).toBe("Vui lòng điền địa chỉ giao hàng.");
        });
    });
    test("Test_TooShortAddress_Should_ReturnTooShortError", () => {
        // Arrange
        const shortAddresses = [
            "Hà Nội", // 7 ký tự
            "123 ABC", // 7 ký tự
            "TPHCM", // 5 ký tự
            "Quận 1", // 7 ký tự
        ];
        shortAddresses.forEach((address) => {
            const result = validateAddress(address);
            expect(result).toBe(
                "Vui lòng điền địa chỉ giao hàng nhiều hơn 10 ký tự."
            );
        });
    });

    // Test lỗi 3: Địa chỉ quá dài (7E.13)
    test("Test_TooLongAddress_Should_ReturnTooLongError", () => {
        const longAddress = "A".repeat(201);

        const result = validateAddress(longAddress);
        expect(result).toBe(
            "Vui lòng điền địa chỉ giao hàng ít hơn 200 ký tự."
        );
    });
});
