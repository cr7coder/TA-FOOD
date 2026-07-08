(function () {
    "use strict";

    // true: lỗi giờ chặn submit; false: lỗi giờ không chặn submit
    const TIME_BLOCKS_SUBMIT = true;

    // Helpers
    function setErr(form, fieldName, message) {
        const input = form.querySelector(`[name="${CSS.escape(fieldName)}"]`);
        if (!input) return;
        input.classList.add("is-invalid");
        let fb = input.parentElement.querySelector(".invalid-feedback");
        if (!fb) {
            fb = document.createElement("div");
            fb.className = "invalid-feedback";
            input.parentElement.appendChild(fb);
        }
        fb.textContent = message;
    }
    function clrErr(form, fieldName) {
        const input = form.querySelector(`[name="${CSS.escape(fieldName)}"]`);
        if (!input) return;
        input.classList.remove("is-invalid");
        const fb = input.parentElement.querySelector(".invalid-feedback");
        if (fb) fb.textContent = "";
    }
    function clrAll(form) {
        form.querySelectorAll(".is-invalid").forEach((el) =>
            el.classList.remove("is-invalid")
        );
        form.querySelectorAll(".invalid-feedback").forEach((el) => el.remove());
        const top = form.querySelector(".form-top-alert");
        if (top) top.remove();
    }
    function topAlert(form, message, type = "danger") {
        let box = form.querySelector(".form-top-alert");
        if (!box) {
            box = document.createElement("div");
            box.className = `form-top-alert alert alert-${type} mb-3`;
            form.prepend(box);
        } else {
            box.className = `form-top-alert alert alert-${type} mb-3`;
        }
        box.textContent = message;

        // Tự động xóa thông báo sau 3 giây nếu là thành công
        if (type === "success") {
            setTimeout(() => {
                if (box && box.parentNode) {
                    box.style.transition = "opacity 0.5s ease";
                    box.style.opacity = "0";
                    setTimeout(() => {
                        if (box && box.parentNode) box.remove();
                    }, 500);
                }
            }, 3000);
        }
    }
    function firstInvalid(form) {
        const el = form.querySelector(".is-invalid");
        if (el) el.focus();
    }

    // Chuẩn hóa giờ → 'HH:mm'
    function coerceTime(el) {
        if (!el) return;
        let s = (el.value || "").trim();
        if (!s) return;

        // HH:mm:ss → HH:mm
        let m = s.match(/^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/);
        if (m) {
            el.value = `${m[1]}:${m[2]}`;
            return;
        }

        // 12h AM/PM → HH:mm
        m = s.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
        if (m) {
            let hh = parseInt(m[1], 10);
            const mm = m[2];
            const ap = m[3].toUpperCase();
            if (ap === "AM") {
                if (hh === 12) hh = 0;
            } else {
                if (hh !== 12) hh += 12;
            }
            el.value = `${String(hh).padStart(2, "0")}:${mm}`;
            return;
        }

        // H:MM hoặc HH:MM (normalize 1 chữ số giờ)
        m = s.match(/^([01]?\d|2[0-3]):([0-5]\d)$/);
        if (m) {
            el.value = `${String(m[1]).padStart(2, "0")}:${m[2]}`;
            return;
        }

        // Các định dạng khác giữ nguyên để validator quyết định
    }

    // Validators A1–A13
    function vTenNhaHang(v) {
        const s = (v || "").trim();
        if (!s) return "Tên nhà hàng không được bỏ trống";
        if (s.length > 100) return "Tên nhà hàng không quá 100 ký tự";
        const re = /^[\p{L}\p{N}\s\-_]+$/u;
        if (!re.test(s)) return "Tên nhà hàng không chứa ký tự đặc biệt";
        return "";
    }
    function vDiaChi(v) {
        const s = (v || "").trim();
        if (!s) return "Địa chỉ không được bỏ trống";
        if (s.length > 200) return "Địa chỉ không quá 200 ký tự";
        return "";
    }
    function vSoDienThoai(v) {
        const s = (v || "").trim();
        if (!s) return "Số điện thoại không được bỏ trống";
        if (!/^\d+$/.test(s)) return "Số điện thoại chỉ chấp nhận chữ số";
        if (s.length < 10 || s.length > 11)
            return "Số điện thoại phải có 10–11 chữ số";
        if (!s.startsWith("0")) return "Số điện thoại phải bắt đầu bằng số 0";
        return "";
    }
    function vEmail(v) {
        const s = (v || "").trim();
        if (!s) return ""; // Cho phép trống
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!re.test(s)) return "Email không đúng định dạng";
        return "";
    }
    function vLatitude(v) {
        const s = (v || "").trim();
        if (!s) return "";
        const n = parseFloat(s);
        if (isNaN(n) || n < -90 || n > 90) return "Vĩ độ phải nằm trong khoảng -90 đến 90";
        return "";
    }
    function vLongitude(v) {
        const s = (v || "").trim();
        if (!s) return "";
        const n = parseFloat(s);
        if (isNaN(n) || n < -180 || n > 180) return "Kinh độ phải nằm trong khoảng -180 đến 180";
        return "";
    }
    function vNumberNonNegative(v, label) {
        const s = (v || "").trim();
        if (!s) return "";
        const n = parseFloat(s);
        if (isNaN(n) || n < 0) return `${label} phải là số không âm`;
        return "";
    }
    function vOpenTime(value) {
        const s = (value || "").trim();
        if (!s) return "Giờ mở cửa không được bỏ trống";
        if (!/^([01]?\d|2[0-3]):([0-5]\d)$/.test(s)) {
            return "Giờ mở cửa không hợp lệ";
        }
        return "";
    }
    function vCloseTime(value, openTimeValue = "") {
        const s = (value || "").trim();
        if (!s) return "Giờ đóng cửa không được bỏ trống";

        if (!/^([01]?\d|2[0-3]):([0-5]\d)$/.test(s)) {
            return "Giờ đóng cửa không hợp lệ";
        }
        const openTime = (openTimeValue || "").trim();
        if (openTime && /^([01]?\d|2[0-3]):([0-5]\d)$/.test(openTime)) {
            // Chuyển đổi thành phút để so sánh
            const [closeHour, closeMin] = s.split(":").map(Number);
            const [openHour, openMin] = openTime.split(":").map(Number);

            const closeMinutes = closeHour * 60 + closeMin;
            const openMinutes = openHour * 60 + openMin;

            if (closeMinutes <= openMinutes) {
                return "Giờ đóng cửa phải sau giờ mở cửa";
            }
        }

        return "";
    }
    function vGioText(val, requiredMsg, invalidMsg) {
        const s = (val || "").trim();
        if (!s) return requiredMsg;
        // Chấp nhận 'HH:mm' sau khi coerceTime, và cả H:MM
        if (!/^([01]?\d|2[0-3]):([0-5]\d)$/.test(s)) return invalidMsg;
        return "";
    }

    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("restaurantForm");
        if (!form) return;

        const csrf =
            document.querySelector('meta[name="csrf-token"]')?.content || "";

        const elTen = form.querySelector('[name="TenNhaHang"]');
        const elDia = form.querySelector('[name="DiaChi"]');
        const elSDT = form.querySelector('[name="SoDienThoai"]');
        const elEmail = form.querySelector('[name="Email"]');
        const elMo = form.querySelector('[name="GioMoCua"]');
        const elDong = form.querySelector('[name="GioDongCua"]');

        // Chuẩn hóa ngay khi load (nếu DB trả HH:mm:ss)
        coerceTime(elMo);
        coerceTime(elDong);

        // Realtime các trường chính
        if (elTen) {
            const h = () => {
                const m = vTenNhaHang(elTen.value);
                m ? setErr(form, "TenNhaHang", m) : clrErr(form, "TenNhaHang");
            };
            elTen.addEventListener("input", h);
            elTen.addEventListener("blur", h);
        }
        if (elDia) {
            const h = () => {
                const m = vDiaChi(elDia.value);
                m ? setErr(form, "DiaChi", m) : clrErr(form, "DiaChi");
            };
            elDia.addEventListener("input", h);
            elDia.addEventListener("blur", h);
        }
        if (elSDT) {
            const h = () => {
                const m = vSoDienThoai(elSDT.value);
                m
                    ? setErr(form, "SoDienThoai", m)
                    : clrErr(form, "SoDienThoai");
            };
            elSDT.addEventListener("input", h);
            elSDT.addEventListener("blur", h);
        }
        if (elEmail) {
            const h = () => {
                const m = vEmail(elEmail.value);
                m ? setErr(form, "Email", m) : clrErr(form, "Email");
            };
            elEmail.addEventListener("input", h);
            elEmail.addEventListener("blur", h);
        }

        // Realtime giờ: chuẩn hóa rồi validate
        if (elMo) {
            const h = () => {
                coerceTime(elMo);
                const m = vOpenTime(elMo.value);
                m ? setErr(form, "GioMoCua", m) : clrErr(form, "GioMoCua");
            };
            elMo.addEventListener("change", h);
            elMo.addEventListener("blur", h);
            elMo.addEventListener("input", h);
        }
        if (elDong) {
            const h = () => {
                coerceTime(elDong);
                const m = vCloseTime(elDong.value, elMo ? elMo.value : "");
                m ? setErr(form, "GioDongCua", m) : clrErr(form, "GioDongCua");
            };
            elDong.addEventListener("change", h);
            elDong.addEventListener("blur", h);
            elDong.addEventListener("input", h);
        }

        function validateMainFields() {
            let hasErr = false;
            if (elTen) {
                const m = vTenNhaHang(elTen.value);
                if (m) {
                    setErr(form, "TenNhaHang", m);
                    hasErr = true;
                }
            }
            if (elDia) {
                const m = vDiaChi(elDia.value);
                if (m) {
                    setErr(form, "DiaChi", m);
                    hasErr = true;
                }
            }
            if (elSDT) {
                const m = vSoDienThoai(elSDT.value);
                if (m) {
                    setErr(form, "SoDienThoai", m);
                    hasErr = true;
                }
            }
            if (elEmail) {
                const m = vEmail(elEmail.value);
                if (m) {
                    setErr(form, "Email", m);
                    hasErr = true;
                }
            }
            
            const elLat = form.querySelector('[name="latitude"]');
            if (elLat) {
                const m = vLatitude(elLat.value);
                if (m) {
                    setErr(form, "latitude", m);
                    hasErr = true;
                }
            }
            
            const elLng = form.querySelector('[name="longitude"]');
            if (elLng) {
                const m = vLongitude(elLng.value);
                if (m) {
                    setErr(form, "longitude", m);
                    hasErr = true;
                }
            }
            
            const elCoBan = form.querySelector('[name="phi_ship_co_ban"]');
            if (elCoBan) {
                const m = vNumberNonNegative(elCoBan.value, "Phí ship cơ bản");
                if (m) {
                    setErr(form, "phi_ship_co_ban", m);
                    hasErr = true;
                }
            }
            
            const elMoiKm = form.querySelector('[name="phi_ship_moi_km"]');
            if (elMoiKm) {
                const m = vNumberNonNegative(elMoiKm.value, "Phí ship mỗi km tiếp theo");
                if (m) {
                    setErr(form, "phi_ship_moi_km", m);
                    hasErr = true;
                }
            }
            
            const elKmMien = form.querySelector('[name="km_mien_phi"]');
            if (elKmMien) {
                const m = vNumberNonNegative(elKmMien.value, "Khoảng cách miễn phí ship");
                if (m) {
                    setErr(form, "km_mien_phi", m);
                    hasErr = true;
                }
            }
            
            return hasErr;
        }
        function validateTimeFields() {
            let hasErr = false;
            if (elMo) {
                coerceTime(elMo);
                const m = vOpenTime(elMo.value);
                if (m) {
                    setErr(form, "GioMoCua", m);
                    hasErr = true;
                }
            }
            if (elDong) {
                coerceTime(elDong);
                const m = vCloseTime(elDong.value, elMo ? elMo.value : "");
                if (m) {
                    setErr(form, "GioDongCua", m);
                    hasErr = true;
                }
            }
            return hasErr;
        }
        
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            clrAll(form);

            // Chuẩn hóa lần nữa trước khi validate
            coerceTime(elMo);
            coerceTime(elDong);

            const mainErr = validateMainFields();
            const timeErr = validateTimeFields();

            if (mainErr || (TIME_BLOCKS_SUBMIT && timeErr)) {
                firstInvalid(form);
                return;
            }

            // AJAX submit
            const submitBtn = form.querySelector(
                'button[type="submit"], .btn-primary'
            );
            const originalHTML = submitBtn ? submitBtn.innerHTML : "";
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<i class="fa fa-spinner fa-spin"></i> Đang lưu...';
            }

            const fd = new FormData(form);
            try {
                const res = await fetch(form.action, {
                    method: (
                        form.getAttribute("method") || "POST"
                    ).toUpperCase(),
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrf,
                    },
                    body: fd,
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Thành công!',
                            text: result.message || "Cập nhật thông tin nhà hàng thành công",
                            icon: 'success',
                            confirmButtonText: 'Đồng ý',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        topAlert(
                            form,
                            result.message || "Cập nhật thông tin nhà hàng thành công",
                            "success"
                        );
                        const alertBox = form.querySelector(".form-top-alert");
                        if (alertBox) alertBox.scrollIntoView({ behavior: "smooth", block: "center" });
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                } else {
                    let msg = result.message || "Có lỗi xảy ra. Vui lòng thử lại.";
                    if (result.errors) {
                        msg = Object.values(result.errors).flat().join(". ");
                    }
                    topAlert(form, msg, "danger");
                    const alertBox = form.querySelector(".form-top-alert");
                    if (alertBox) alertBox.scrollIntoView({ behavior: "smooth", block: "center" });
                }
            } catch (err) {
                topAlert(form, "Không thể kết nối đến máy chủ. Vui lòng thử lại.", "danger");
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            }
        });
    });
})();
