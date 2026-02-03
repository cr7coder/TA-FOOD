/**
 * Jest test file for validateQuantity function
 * Run with: npm test
 */

// Mock implementation of validateQuantity function
let mockAlerts = [];

function showAlert(type, message) {
    mockAlerts.push({ type, message });
}

async function validateQuantity(id, input) {
    let numberValue = input.value.trim();
    if (isNaN(numberValue)) {
        showAlert('danger', 'Số lượng món ăn phải là số');
        return null;
    }
    if (!Number.isInteger(parseFloat(numberValue))) {
        showAlert('danger', 'Số lượng món ăn phải là số nguyên');
        return null;
    }
    if (numberValue <= 0) {
        showAlert('danger', 'Số lượng món ăn phải lớn hơn 0');
        return null;
    }
    return numberValue;
}

describe("validateQuantity Function Tests", () => {
    beforeEach(() => {
        mockAlerts = [];
    });
    // Test thành công với input hợp lệ
    test("Test_ValidPositiveInteger_Should_ReturnSameNumber", async () => {
        const input = { value: "5" };
        const result = await validateQuantity("test-id", input);
        expect(result).toBe("5");
        expect(mockAlerts).toHaveLength(0);
    });
    test("Test_NonNumericInput_Should_ReturnNull", async () => {
        const input = { value: "abc" };
        const result = await validateQuantity("test-id", input);
        expect(result).toBeNull();
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].type).toBe("danger");
        expect(mockAlerts[0].message).toBe("Số lượng món ăn phải là số");
    });
    test("Test_DecimalNumber_Should_ReturnNull", async () => {
        const input = { value: "5.5" };
        const result = await validateQuantity("test-id", input);
        expect(result).toBeNull();
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].type).toBe("danger");
        expect(mockAlerts[0].message).toBe("Số lượng món ăn phải là số nguyên");
    });
    test("Test_ZeroOrNegativeNumber_Should_ReturnNull", async () => {
        const input = { value: "0" };
        const result = await validateQuantity("test-id", input);
        expect(result).toBeNull();
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].type).toBe("danger");
        expect(mockAlerts[0].message).toBe("Số lượng món ăn phải lớn hơn 0");
    });
});
