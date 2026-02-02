(function () {
    ("use strict");

    const ALLOWED_CATEGORIES = ["Cơm", "Bún", "Phở", "Mì", "Trà"];
    const ALLOWED_IMAGE_EXTS = ["jpg", "jpeg", "png"];
    const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

    // ==== A) Chuẩn hóa tên & cache kết quả check unique ====
    function normalizeName(s) {
        return (s || "")
            .toString()
            .trim()
            .normalize("NFD")
            .replace(/\p{Diacritic}/gu, "")
            .replace(/\s+/g, " ")
            .toLowerCase();
    }

    // cache kết quả lần check gần nhất
    let ORIGINAL_NAME_NORM = ""; // tên gốc khi edit (để không báo trùng)
    let LAST_UNIQUE_NAME_NORM = ""; // tên đã check gần nhất
    let LAST_UNIQUE_EXISTS = false; // true nếu backend trả 409 = trùng

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
        box.textContent = message || "";
    }
    function firstInvalid(form) {
        const el = form.querySelector(".is-invalid");
        if (el) el.focus();
    }
    function debounce(fn, wait = 300) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), wait);
        };
    }

    function vTen(v) {
        v = (v || "").trim();
        if (!v) return "Tên món ăn không được bỏ trống (2E.1)";
        if (v.length > 100) return "Tên món ăn không quá 100 ký tự (2E.2)";
        if (!/^[\p{L}\p{N}\s\-_]+$/u.test(v))
            return "Tên món ăn không được chứa ký tự đặcc biệt (2E.3)";
        const nv = normalizeName(v);
        if (
            nv &&
            nv !== ORIGINAL_NAME_NORM &&
            LAST_UNIQUE_NAME_NORM === nv &&
            LAST_UNIQUE_EXISTS
        ) {
            return "Tên món ăn đã tồn tại trong hệ thống";
        }
        return "";
    }
    function vGia(v) {
        v = (v ?? "").toString().trim();
        if (!v) return "Giá món ăn không được bỏ trống (2E.5)";
        if (!/^-?\d+(\.\d+)?$/.test(v))
            return "Giá món ăn chỉ được nhập số (2E.7)";
        if (!(Number(v) > 0)) return "Giá món ăn phải lớn hơn 0 (2E.6)";
        return "";
    }
    function vDanhMuc(v) {
        v = (v || "").trim();
        if (!v) return "Danh mục không được bỏ trống (2E.8)";
        if (!ALLOWED_CATEGORIES.includes(v))
            return "Danh mục không hợp lệ (2E.9)";
        return "";
    }
    function vHinh(file, mode) {
        if (!file) {
            if (mode === "create")
                return "Hình ảnh món ăn không được bỏ trống (2E.11)";
            return "";
        }
        const ext = (file.name.split(".").pop() || "").toLowerCase();
        if (!ALLOWED_IMAGE_EXTS.includes(ext))
            return "Hình ảnh chỉ hỗ trợ jpg, jpeg, png (2E.12)";
        if (file.size > MAX_IMAGE_BYTES)
            return "Hình ảnh không được vượt quá 5MB (2E.13)";
        return "";
    }

    async function vTenUniqueAsync(form, value) {
        const url = form.dataset.checkNameUrl;
        const original = form.dataset.originalName || "";
        const mode = (form.dataset.mode || "create").toLowerCase();
        const foodId = form.dataset.foodId || "";
        if (!url) return "";
        const v = (value || "").trim();
        if (!v) return "";
        if (mode === "edit" && original && v === original) return "";
        try {
            const qs = new URLSearchParams({ name: v });
            if (foodId) qs.set("ignore", foodId);
            const res = await fetch(`${url}?${qs.toString()}`, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            LAST_UNIQUE_NAME_NORM = normalizeName(v);
            LAST_UNIQUE_EXISTS = res.status === 409;

            if (res.status === 409)
                return "Tên món ăn đã tồn tại trong hệ thống";
            if (res.status === 204) return "";
            return "";
        } catch {
            return "";
        }
    }

    function snapshotForm(form) {
        const get = (sel) =>
            (form.querySelector(sel)?.value || "").toString().trim();
        return {
            TenMonAn: get('[name="TenMonAn"]'),
            DanhMuc: get('[name="DanhMuc"]'),
            MoTa: get('[name="MoTa"]'),
            Gia: get('[name="Gia"]'),
            TrangThai: get('[name="TrangThai"]'),
        };
    }
    function sameSnapshot(a, b) {
        const keys = ["TenMonAn", "DanhMuc", "MoTa", "Gia", "TrangThai"];
        return keys.every((k) => (a[k] ?? "") === (b[k] ?? ""));
    }

    function normalizeServerMessage(field, rawMessage) {
        const msg = (rawMessage || "").toString();
        if (field === "HinhAnh") {
            if (/required/i.test(msg))
                return "Hình ảnh món ăn không được bỏ trống";
            if (/mimes|type|jpg|jpeg|png/i.test(msg))
                return "Hình ảnh chỉ hỗ trợ jpg, jpeg, png ";
            if (/max|5 ?mb|too large|greater than/i.test(msg))
                return "Hình ảnh không được vượt quá 5MB ";
        }
        if (field === "TenMonAn") {
            if (/required/i.test(msg)) return "Tên món ăn không được bỏ trống";
            if (/max|greater than 100|must not be greater than 100/i.test(msg))
                return "Tên món ăn không quá 100 ký tự ";
            if (/regex|format|invalid/i.test(msg))
                return "Tên món ăn không được chứa ký tự đặc biệt ";
            if (/taken|exists|đã tồn tại/i.test(msg))
                return "Tên món ăn đã tồn tại trong hệ thống";
        }
        if (field === "Gia") {
            if (/required/i.test(msg)) return "Giá món ăn không được bỏ trống ";
            if (/numeric|number/i.test(msg))
                return "Giá món ăn chỉ được nhập số";
            if (/greater than 0|must be greater than 0|gt/i.test(msg))
                return "Giá món ăn phải lớn hơn 0";
        }
        if (field === "DanhMuc") {
            if (/required/i.test(msg)) return "Danh mục không được bỏ trống";
            if (/in|invalid|không hợp lệ/i.test(msg))
                return "Danh mục không hợp lệ";
        }
        return msg;
    }

    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("foodForm");
        if (!form) return;

        const mode = (form.dataset.mode || "create").toLowerCase();
        const ajaxSubmit = (form.dataset.ajax || "1") === "1";
        const csrf =
            document.querySelector('meta[name="csrf-token"]')?.content || "";

        const elTen = form.querySelector('[name="TenMonAn"]');
        const elGia = form.querySelector('[name="Gia"]');
        const elDanhMuc = form.querySelector('[name="DanhMuc"]');
        const elHinh = form.querySelector('[name="HinhAnh"]');

        form.dataset.initial = JSON.stringify(snapshotForm(form));

        if (elTen) {
            const runSync = () => {
                const m = vTen(elTen.value);
                m ? setErr(form, "TenMonAn", m) : clrErr(form, "TenMonAn");
            };

            const runUnique = debounce(async () => {
                // chỉ gọi unique khi qua các check sync cơ bản (rỗng/độ dài/ký tự)
                if (vTen(elTen.value)) return;

                const m = await vTenUniqueAsync(form, elTen.value);

                // Sau khi có kết quả async, GỌI LẠI runSync() để vTen đọc cache và hiển thị ngay
                runSync();

                // Nếu vẫn muốn setErr trực tiếp theo kết quả async (không chờ lần gõ tiếp theo)
                if (m) setErr(form, "TenMonAn", m);
                else if (!vTen(elTen.value)) clrErr(form, "TenMonAn");
            }, 400);

            elTen.addEventListener("input", () => {
                runSync();
                runUnique();
            });
            elTen.addEventListener("blur", () => {
                runSync();
                runUnique();
            });
        }
        if (elGia) {
            const h = () => {
                const m = vGia(elGia.value);
                m ? setErr(form, "Gia", m) : clrErr(form, "Gia");
            };
            elGia.addEventListener("input", h);
            elGia.addEventListener("blur", h);
        }
        if (elDanhMuc) {
            const h = () => {
                const m = vDanhMuc(elDanhMuc.value);
                m ? setErr(form, "DanhMuc", m) : clrErr(form, "DanhMuc");
            };
            elDanhMuc.addEventListener("change", h);
            elDanhMuc.addEventListener("blur", h);
        }
        if (elHinh) {
            elHinh.addEventListener("change", () => {
                const file = elHinh.files && elHinh.files[0];
                const m = vHinh(file, mode);
                m ? setErr(form, "HinhAnh", m) : clrErr(form, "HinhAnh");
            });
        }

        form.addEventListener("submit", async (e) => {
            clrAll(form);
            let hasErr = false;

            if (elTen) {
                const mSync = vTen(elTen.value);
                if (mSync) {
                    setErr(form, "TenMonAn", mSync);
                    hasErr = true;
                } else {
                    const mUniq = await vTenUniqueAsync(form, elTen.value);
                    // cập nhật giao diện dựa trên cache mới
                    const mAfter = vTen(elTen.value); // vTen sẽ đọc cache vừa cập nhật
                    if (mUniq || mAfter) {
                        setErr(form, "TenMonAn", mUniq || mAfter);
                        hasErr = true;
                    }
                }
            }
            if (elGia) {
                const m = vGia(elGia.value);
                if (m) {
                    setErr(form, "Gia", m);
                    hasErr = true;
                }
            }
            if (elDanhMuc) {
                const m = vDanhMuc(elDanhMuc.value);
                if (m) {
                    setErr(form, "DanhMuc", m);
                    hasErr = true;
                }
            }
            if (elHinh) {
                const file = elHinh.files && elHinh.files[0];
                const m = vHinh(file, mode);
                if (m) {
                    setErr(form, "HinhAnh", m);
                    hasErr = true;
                }
            }

            if (hasErr) {
                e.preventDefault();
                firstInvalid(form);
                return;
            }

            if (mode === "edit") {
                const init = JSON.parse(form.dataset.initial || "{}");
                const current = snapshotForm(form);
                const fileSelected = !!(
                    elHinh &&
                    elHinh.files &&
                    elHinh.files.length > 0
                );
                if (sameSnapshot(init, current) && !fileSelected) {
                    e.preventDefault();
                    topAlert(form, "Cập nhật món ăn thành công", "success");
                    setTimeout(() => {
                        const indexUrl =
                            form.dataset.indexUrl || "/seller/foods";
                        window.location.href = indexUrl;
                    }, 2000);
                    return;
                }
            }

            if (!ajaxSubmit) return;

            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const html0 = submitBtn ? submitBtn.innerHTML : "";
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

                const ct = res.headers.get("content-type") || "";
                const isJson = ct.includes("application/json");

                let data = {};
                if (isJson) {
                    try {
                        data = await res.json();
                    } catch {}
                } else {
                    // ĐỌC BỎ HTML để tránh hiển thị
                    try {
                        await res.text();
                    } catch {}
                }

                if (!res.ok) {
                    if (res.status === 422 && data?.errors) {
                        Object.entries(data.errors).forEach(([field, msgs]) => {
                            const input = form.querySelector(
                                `[name="${field}"]`
                            );
                            if (input && input.classList.contains("is-invalid"))
                                return;
                            const firstMsg = Array.isArray(msgs)
                                ? msgs[0]
                                : String(msgs);
                            setErr(
                                form,
                                field,
                                normalizeServerMessage(field, firstMsg)
                            );
                        });
                        firstInvalid(form);
                        return;
                    }

                    const msg = (data?.message || "").toString();
                    const lc = msg.toLowerCase();
                    if (
                        lc.includes("không có thông tin nào được cập nhật") ||
                        lc.includes("3e.14")
                    ) {
                        topAlert(form, "Cập nhật món ăn thành công", "success");
                        return;
                    }
                    if (
                        res.status >= 500 ||
                        lc.includes("3e.15") ||
                        lc.includes("lỗi hệ thống")
                    ) {
                        topAlert(
                            form,
                            mode === "edit"
                                ? "Sửa món thất bại vì lỗi hệ thống. Vui lòng thử lại (3E.15)"
                                : "Thêm món thất bại vì lỗi hệ thống. Vui lòng thử lại",
                            "danger"
                        );
                        return;
                    }
                    topAlert(
                        form,
                        msg || "Có lỗi xảy ra. Vui lòng thử lại.",
                        "danger"
                    );
                    return;
                }

                // ===== Thành công =====
                if (isJson) {
                    topAlert(
                        form,
                        data.message ||
                            (mode === "edit"
                                ? "Cập nhật món ăn thành công"
                                : "Thêm món ăn thành công"),
                        "success"
                    );
                    const redirectUrl = data.redirect || form.dataset.indexUrl;
                    if (redirectUrl)
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 700);
                } else {
                    // Response là HTML/redirect → KHÔNG hiển thị HTML
                    topAlert(
                        form,
                        mode === "edit"
                            ? "Cập nhật món ăn thành công!"
                            : "Thêm món ăn thành công!",
                        "success"
                    );
                    // Nếu fetch theo sau redirect, res.redirected sẽ true và res.url là URL đích
                    const redirectUrl =
                        form.dataset.indexUrl ||
                        (res.redirected && res.url ? res.url : "");
                    if (redirectUrl)
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 2000);
                }
            } catch (err) {
                topAlert(
                    form,
                    mode === "edit"
                        ? "Sửa món thất bại vì lỗi hệ thống. Vui lòng thử lại (3E.15)"
                        : "Có lỗi mạng. Vui lòng thử lại.",
                    "danger"
                );
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = html0;
                }
            }
        });
    });
})();
