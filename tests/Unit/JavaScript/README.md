# JavaScript Testing for validateQuantity Function

npx jest tests/JavaScript/validatePhone.test.js --silent

## Tổng quan

Dự án này bao gồm hệ thống test toàn diện cho hàm `validateQuantity` trong ứng dụng Laravel Food Ordering.

## Cấu trúc Test

### 1. HTML Test File

-   **File**: `tests/JavaScript/validateQuantity.test.html`
-   **Mục đích**: Test thủ công và trực quan trong browser
-   **Tính năng**:
    -   Manual testing với input field
    -   Automated test suite với 15+ test cases
    -   UI hiển thị kết quả real-time
    -   Alert system để test showAlert function

### 2. Jest Test File

-   **File**: `tests/Unit/JavaScript/validateQuantity.test.js`
-   **Mục đích**: Professional unit testing với Jest framework
-   **Coverage**: 21 test cases bao gồm:
    -   Valid input cases (6 tests)
    -   Invalid input cases (10 tests)
    -   Edge cases (3 tests)
    -   Regex pattern tests (2 tests)

## Chạy Tests

### Jest Tests (Recommended)

```bash
# Chạy tất cả tests
npm test

# Chạy tests với watch mode
npm run test:watch

# Chạy tests với coverage report
npm run test:coverage
```

### HTML Tests

```bash
# Mở file HTML trong browser
start tests/JavaScript/validateQuantity.test.html
```

## Test Cases

### ✅ Valid Input Cases

1. **Positive integers**: `'1'`, `'5'`, `'10'`, `'999'`
2. **Zero value**: `'0'`
3. **Numbers with spaces**: `'  5  '` → `'5'`
4. **Large numbers**: `'999999999'`

### ❌ Invalid Input Cases

1. **Empty string**: `''` → `null`
2. **Non-numeric**: `'abc'`, `'không'` → `null`
3. **Decimal numbers**: `'2.5'`, `'5.0'` → `null`
4. **Negative numbers**: `'-5'`, `'-1'` → `null`
5. **Special characters**: `'+5'`, `'1e5'` → `null`
6. **Only spaces**: `'   '` → `null`

### 🔬 Edge Cases

1. **Very large numbers**: `'999999999'`
2. **Single characters**: `'a'`, `'!'`, `'@'`
3. **Special symbols**: `'!@#$%^&*()'`

## Hàm validateQuantity

```javascript
async function validateQuantity(id, input) {
    let Number = input.value.trim();
    if (!/^-?\d+$/.test(Number)) {
        showAlert("danger", "Vui lòng nhập số nguyên dương");
        return null;
    }
    if (Number < 0) {
        showAlert("danger", "Số lượng món ăn phải lớn hơn hoặc bằng 0");
        return null;
    }
    return Number;
}
```

## Validation Rules

### Rule 1: Format Check

-   **Regex**: `/^-?\d+$/`
-   **Mục đích**: Chỉ chấp nhận số nguyên (có thể âm)
-   **Reject**: Decimal, text, special chars, scientific notation

### Rule 2: Non-negative Check

-   **Condition**: `Number < 0`
-   **Mục đích**: Không cho phép số âm
-   **Accept**: `0` và số dương

## Dependencies

```json
{
    "jest": "^29.7.0",
    "jest-environment-jsdom": "^29.7.0",
    "@babel/core": "^7.28.4",
    "@babel/preset-env": "^7.28.3",
    "babel-jest": "^30.2.0"
}
```

## Configuration Files

### Jest Config (`jest.config.js`)

```javascript
export default {
    testEnvironment: "jsdom",
    testMatch: ["**/tests/Unit/JavaScript/**/*.test.js"],
    setupFilesAfterEnv: ["<rootDir>/tests/Unit/JavaScript/setup.js"],
    collectCoverage: true,
    verbose: true,
};
```

### Babel Config (`.babelrc`)

```json
{
    "presets": ["@babel/preset-env"]
}
```

## Kết quả Test Gần nhất

```
✓ 21 tests passed
✓ 0 tests failed
✓ Coverage: 100% function coverage
✓ All validation rules working correctly
```

### Test Categories:

-   **Valid Input Cases**: 6/6 ✅
-   **Invalid Input Cases**: 10/10 ✅
-   **Edge Cases**: 3/3 ✅
-   **Regex Pattern Tests**: 2/2 ✅

## Troubleshooting

### Common Issues:

1. **Jest module error**:

    ```bash
    # Fix: Install dependencies
    npm install
    ```

2. **ES Module issues**:

    ```bash
    # Ensure package.json has "type": "module"
    ```

3. **HTML test not working**:
    - Open directly in browser
    - Check console for errors
    - Ensure all functions are defined

## Integration với Laravel

Hàm `validateQuantity` được sử dụng trong:

-   `resources/views/foods/_cart-scripts.blade.php`
-   Cart functionality
-   Order validation
-   Frontend input validation

## Best Practices

1. **Test both positive and negative cases**
2. **Include edge cases và boundary values**
3. **Mock external dependencies (showAlert)**
4. **Use descriptive test names**
5. **Group related tests với describe blocks**
6. **Reset state between tests**

## Contribute

Để thêm test cases:

1. Thêm vào `testCases` array trong HTML file
2. Thêm test method trong Jest file
3. Update documentation
4. Chạy tests để verify

---

**Author**: Food Ordering Laravel Team  
**Last Updated**: October 7, 2025  
**Version**: 1.0.0
