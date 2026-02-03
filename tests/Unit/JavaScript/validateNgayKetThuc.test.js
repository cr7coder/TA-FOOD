/**
 * Jest test file for validateNgayKetThuc function
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

function validateNgayKetThuc() {
    const name = "NgayKetThuc";
    syncDateInputs();
    const rawValue = getRawDateValue(name);
    const parsedDMY = parseDMY(rawValue);
    const start = getDateValue("NgayBatDau");
    const v = getDateValue(name);
    if (!rawValue) {
        showFieldError(name, "Ngày kết thúc không được bỏ trống");
        return false;
    }
    if (rawValue) {
        if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(rawValue)) {
            showFieldError(name, "Ngày kết thúc không hợp lệ");
            return false;
        }
        if (!parsedDMY) {
            showFieldError(name, "Ngày kết thúc không hợp lệ");
            return false;
        }
    }
    if (start && v < start) {
        showFieldError(name, "Ngày kết thúc phải sau hoặc bằng ngày bắt đầu");
        return false;
    }
    return true;
}

describe("validateNgayKetThuc Function Tests", () => {
    beforeEach(() => {
        mockAlerts = [];
        document.body.innerHTML = `
            <input name="NgayBatDau" value="" />
            <input name="NgayKetThuc" value="" />
        `;
    });

    // Test thành công
    test("Test_ValidEndDate_Should_ReturnTrue", () => {
        const startDate = "2024-12-25";
        const endDate = "2024-12-31";
        const endDateDMY = "31/12/2024";

        const startInput = document.querySelector('[name="NgayBatDau"]');
        const endInput = document.querySelector('[name="NgayKetThuc"]');
        startInput.value = startDate;
        endInput.value = endDate;

        // Mock getRawDateValue to return DMY format for end date
        getRawDateValue = jest.fn((fieldName) => {
            if (fieldName === "NgayKetThuc") return endDateDMY;
            return "";
        });
        const result = validateNgayKetThuc();
        expect(result).toBe(true);
        expect(mockAlerts).toHaveLength(0);
    });
    test("Test_EmptyEndDate_Should_ReturnFalse_And_ShowEmptyAlert", () => {
        const endInput = document.querySelector('[name="NgayKetThuc"]');
        endInput.value = "";

        // Reset getRawDateValue to original function
        getRawDateValue = jest.fn((fieldName) => {
            const input = qByName(fieldName);
            return input?.value?.trim() || "";
        });
        const result = validateNgayKetThuc();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Ngày kết thúc không được bỏ trống");
    });
    test("Test_InvalidFormat_Should_ReturnFalse_And_ShowFormatAlert", () => {
        // Arrange
        const endInput = document.querySelector('[name="NgayKetThuc"]');
        endInput.value = "2024-12-31";

        // Mock getRawDateValue to return invalid format
        getRawDateValue = jest.fn(() => "2024-12-31");
        const result = validateNgayKetThuc();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Ngày kết thúc không hợp lệ");
    });
    test("Test_InvalidDate_Should_ReturnFalse_And_ShowInvalidAlert", () => {
        const endInput = document.querySelector('[name="NgayKetThuc"]');
        endInput.value = "2024-02-31";

        // Mock getRawDateValue to return invalid date
        getRawDateValue = jest.fn(() => "31/02/2024");
        const result = validateNgayKetThuc();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Ngày kết thúc không hợp lệ");
    });
    test("Test_EndDateBeforeStartDate_Should_ReturnFalse_And_ShowOrderAlert", () => {
        // Arrange
        const startDate = "2024-12-31";
        const endDate = "2024-12-25";
        const endDateDMY = "25/12/2024";

        const startInput = document.querySelector('[name="NgayBatDau"]');
        const endInput = document.querySelector('[name="NgayKetThuc"]');
        startInput.value = startDate;
        endInput.value = endDate;

        // Mock getRawDateValue to return DMY format for end date
        getRawDateValue = jest.fn((fieldName) => {
            if (fieldName === "NgayKetThuc") return endDateDMY;
            return "";
        });
        const result = validateNgayKetThuc();
        expect(result).toBe(false);
        expect(mockAlerts).toHaveLength(1);
        expect(mockAlerts[0].message).toBe("Ngày kết thúc phải sau hoặc bằng ngày bắt đầu");
    });

    // // Test edge case: Ngày kết thúc bằng ngày bắt đầu
    // test("Test_EndDateEqualStartDate_Should_ReturnTrue", () => {
    //     // Arrange
    //     const startDate = "2024-12-25";
    //     const endDate = "2024-12-25";
    //     const endDateDMY = "25/12/2024";

    //     const startInput = document.querySelector('[name="NgayBatDau"]');
    //     const endInput = document.querySelector('[name="NgayKetThuc"]');
    //     startInput.value = startDate;
    //     endInput.value = endDate;

    //     // Mock getRawDateValue to return DMY format for end date
    //     getRawDateValue = jest.fn((fieldName) => {
    //         if (fieldName === "NgayKetThuc") return endDateDMY;
    //         return "";
    //     });

    //     // Act
    //     const result = validateNgayKetThuc();

    //     // Assert
    //     expect(result).toBe(true);
    //     expect(mockAlerts).toHaveLength(0);
    // });
});