/**
 * Jest test file for validateMaCode function
 * Run with: npm test
 */

// Mock implementation
let mockAlerts = [];
let mockFetchResponse = { exists: false };

function showFieldError(name, message) {
    mockAlerts.push({ name, type: "danger", message });
}

function clearFieldError(name) {
    mockAlerts = mockAlerts.filter(alert => alert.name !== name);
}

function qByName(name) {
    return document.querySelector(`[name="${name}"]`);
}

// Mock fetch API
global.fetch = jest.fn(() =>
    Promise.resolve({
        json: () => Promise.resolve(mockFetchResponse),
    })
);

function checkMaCodeExistsAsync(code) {
    return new Promise((resolve, reject) => {
        try {
            fetch("/api/check-code?MaCode=" + encodeURIComponent(code))
                .then((res) => res.json())
                .then((data) => resolve(data.exists))
                .catch(() => resolve(false));
        } catch (e) {
            reject(e);
        }
    });
}

async function validateMaCode() {
    const name = "MaCode";
    const el = qByName(name);
    const maCode = (el?.value || "").trim();
    if (!maCode) {
        showFieldError(name, "Mã giảm giá không được bỏ trống");
        return false;
    }
    if (maCode.length > 12) {
        showFieldError(name, "Mã giảm giá không quá 12 ký tự");
        return false;
    }
    if (!/^[A-Za-z0-9]+$/.test(maCode)) {
        showFieldError(name, "Mã giảm giá không chứa ký tự đặc biệt");
        return false;
    }
    const exists = await checkMaCodeExistsAsync(maCode);
    if (exists) {
        showFieldError(name, "Mã giảm giá đã tồn tại");
        return false;
    }
    clearFieldError(name);
    return true;
}

describe("validateMaCode Function Tests", () => {
    beforeEach(() => {
        mockAlerts = [];
        mockFetchResponse = { exists: false };
        document.body.innerHTML = '<input name="MaCode" value="" />';
        fetch.mockClear();
    });

    // Test thành công
    test("Test_ValidMaCode_Should_ReturnTrue", async () => {
        const input = document.querySelector('[name="MaCode"]');
        input.value = "VALID123";

        const result = await validateMaCode();
        expect(result).toBe(true);
        expect(mockAlerts).toHaveLength(0);
    });
    test("Test_EmptyMaCode_Should_ReturnFalse_And_ShowEmptyAlert", async () => {
        const input = document.querySelector('[name="MaCode"]');
        input.value = "";

        const result = await validateMaCode();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Mã giảm giá không được bỏ trống");
    });
    test("Test_TooLongMaCode_Should_ReturnFalse_And_ShowLengthAlert", async () => {
        const input = document.querySelector('[name="MaCode"]');
        input.value = "VERYLONGCODE123";

        const result = await validateMaCode();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Mã giảm giá không quá 12 ký tự");
    });

    test("Test_SpecialCharacters_Should_ReturnFalse_And_ShowFormatAlert", async () => {
        // Arrange
        const input = document.querySelector('[name="MaCode"]');
        input.value = "CODE@123";

        const result = await validateMaCode();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Mã giảm giá không chứa ký tự đặc biệt");
    });

    test("Test_ExistingMaCode_Should_ReturnFalse_And_ShowExistsAlert", async () => {
        mockFetchResponse = { exists: true };
        const input = document.querySelector('[name="MaCode"]');
        input.value = "SALE10";

        const result = await validateMaCode();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Mã giảm giá đã tồn tại");
    });
});