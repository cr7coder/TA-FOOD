/**
 * Jest test file for validateNgayBatDau function
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

function syncDateInputs() {
    // Mock sync function
}

function getRawDateValue(fieldName) {
    const input = qByName(fieldName);
    return input?.value?.trim() || "";
}

function getDateValue(fieldName) {
    const input = qByName(fieldName);
    return input?.value?.trim() || "";
}

function todayYMD() {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    return `${y}-${m}-${day}`;
}

function parseDMY(s) {
    if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s || "")) return null;
    const [d, m, y] = s.split("/").map(Number);

    if (d < 1 || d > 31 || m < 1 || m > 12 || y < 1900 || y > 2100) {
        return null;
    }

    const dt = new Date(y, m - 1, d);
    if (
        dt.getFullYear() !== y ||
        dt.getMonth() !== m - 1 ||
        dt.getDate() !== d
    ) {
        return null;
    }
    return dt;
}

function validateNgayBatDau() {
    const name = "NgayBatDau";
    syncDateInputs();
    const rawValue = getRawDateValue(name);
    if (!rawValue) {
        showFieldError(name, "Ngày bắt đầu không được bỏ trống");
        return false;
    }
    if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(rawValue)) {
        showFieldError(name, "Ngày bắt đầu không hợp lệ");
        return false;
    }
    const parsedDMY = parseDMY(rawValue);
    if (!parsedDMY) {
        showFieldError(name, "Ngày bắt đầu không hợp lệ");
        return false;
    }
    const v = getDateValue(name);
    if (v < todayYMD()) {
        showFieldError(name, "Ngày bắt đầu phải lớn hơn hoặc bằng hôm nay");
        return false;
    }
    return true;
}

describe("validateNgayBatDau Function Tests", () => {
    beforeEach(() => {
        mockAlerts = [];
        document.body.innerHTML = '<input name="NgayBatDau" value="" />';
    });

    // Test thành công
    test("Test_ValidFutureDate_Should_ReturnTrue", () => {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = `${tomorrow.getDate()}/${tomorrow.getMonth() + 1}/${tomorrow.getFullYear()}`;
        const tomorrowYMD = `${tomorrow.getFullYear()}-${String(tomorrow.getMonth() + 1).padStart(2, "0")}-${String(tomorrow.getDate()).padStart(2, "0")}`;

        const input = document.querySelector('[name="NgayBatDau"]');
        input.value = tomorrowYMD;

        // Mock getRawDateValue to return DMY format
        getRawDateValue = jest.fn(() => tomorrowStr);
        const result = validateNgayBatDau();
        expect(result).toBe(true);
        expect(mockAlerts).toHaveLength(0);
    });
    test("Test_EmptyDate_Should_ReturnFalse_And_ShowEmptyAlert", () => {
        const input = document.querySelector('[name="NgayBatDau"]');
        input.value = "";

        // Reset getRawDateValue to original function
        getRawDateValue = jest.fn((fieldName) => {
            const input = qByName(fieldName);
            return input?.value?.trim() || "";
        });

        const result = validateNgayBatDau();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Ngày bắt đầu không được bỏ trống");
    });

    test("Test_InvalidFormat_Should_ReturnFalse_And_ShowFormatAlert", () => {
        const input = document.querySelector('[name="NgayBatDau"]');
        input.value = "2024-12-25";

        // Mock getRawDateValue to return invalid format
        getRawDateValue = jest.fn(() => "2024-12-25");
        const result = validateNgayBatDau();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Ngày bắt đầu không hợp lệ");
    });
    test("Test_InvalidDate_Should_ReturnFalse_And_ShowInvalidAlert", () => {
        const input = document.querySelector('[name="NgayBatDau"]');
        input.value = "2024-02-31";

        // Mock getRawDateValue to return invalid date
        getRawDateValue = jest.fn(() => "31/02/2024");
        const result = validateNgayBatDau();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Ngày bắt đầu không hợp lệ");
    });
    test("Test_PastDate_Should_ReturnFalse_And_ShowPastAlert", () => {
        // Arrange
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const yesterdayStr = `${yesterday.getDate()}/${yesterday.getMonth() + 1}/${yesterday.getFullYear()}`;
        const yesterdayYMD = `${yesterday.getFullYear()}-${String(yesterday.getMonth() + 1).padStart(2, "0")}-${String(yesterday.getDate()).padStart(2, "0")}`;

        const input = document.querySelector('[name="NgayBatDau"]');
        input.value = yesterdayYMD;
        // Mock getRawDateValue to return DMY format
        getRawDateValue = jest.fn(() => yesterdayStr);

        const result = validateNgayBatDau();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Ngày bắt đầu phải lớn hơn hoặc bằng hôm nay");
    });
});