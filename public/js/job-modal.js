(function () {
    "use strict";
    if (typeof selectedEmployees === "undefined") {
        window.selectedEmployees = [];
    }

    function renderSelectedEmployees(readOnly = false) {
        const form = document.getElementById("jobForm");
        const selectedBox = document.getElementById("selectedEmployees");
        if (!form || !selectedBox) return;

        selectedBox.innerHTML = "";
        form.querySelectorAll('input[name="employee_ids[]"]').forEach((el) =>
            el.remove()
        );

        selectedEmployees.forEach((emp) => {
            const chip = document.createElement("span");
            chip.className = "badge bg-primary d-flex align-items-center";
            chip.style.gap = "6px";
            chip.textContent = `${emp.nik} - ${emp.name}`;

            // hanya buat tombol hapus kalau bukan mode readOnly dan jobMode = receiver
            if (!readOnly && window.jobMode === "receiver") {
                const closeBtn = document.createElement("button");
                closeBtn.type = "button";
                closeBtn.className = "btn-close btn-close-white btn-sm ms-1";
                closeBtn.setAttribute("aria-label", "Remove");
                closeBtn.onclick = function () {
                    chip.remove();
                    selectedEmployees = selectedEmployees.filter(
                        (e) => e.id !== emp.id
                    );
                    form.querySelectorAll(
                        `input[name="employee_ids[]"][value="${emp.id}"]`
                    ).forEach((el) => el.remove());
                };
                chip.appendChild(closeBtn);
            }

            selectedBox.appendChild(chip);

            // hidden input hanya ditambahkan kalau tidak readOnly
            if (!readOnly) {
                const hidden = document.createElement("input");
                hidden.type = "hidden";
                hidden.name = "employee_ids[]";
                hidden.value = emp.id;
                form.appendChild(hidden);
            }
        });
    }

    function getEl(id) {
        return document.getElementById(id);
    }

    function showModal() {
        const modalEl = getEl("jobModal");
        if (!modalEl) {
            console.error("jobModal element not found in DOM");
            return null;
        }
        return new bootstrap.Modal(modalEl);
    }

    // Reset semua input & chips
    function resetFormFields(form) {
        form.reset();

        // Reset manual beberapa field (karena ada yang readonly/disabled)
        if (getEl("jobStatus")) getEl("jobStatus").value = "pending";
        if (getEl("jobStatusText")) getEl("jobStatusText").value = "pending";

        // Bersihkan hidden input employee & chips
        form.querySelectorAll('input[name="employee_ids[]"]').forEach((el) =>
            el.remove()
        );
        getEl("selectedEmployees").innerHTML = "";

        let ticket = getEl("jobTicketNumber");
        if (ticket) ticket.value = "";
    }

    window.openAddJobModal = function () {
        try {
            const modal = showModal();
            if (!modal) return;

            const form = getEl("jobForm");

            // Atur route & method
            form.setAttribute("action", form.dataset.routeStore);
            getEl("jobFormMethod").value = "POST";

            // Reset field
            resetFormFields(form);

            // Title & tombol
            getEl("jobModalTitle").innerText = "Tambah Job";
            getEl("jobModalSubmit").innerText = "Simpan";

            // Enable/disable input search sesuai mode
            let empSearch = getEl("employee_search");
            if (empSearch) {
                if (window.jobMode === "giver") {
                    empSearch.setAttribute("disabled", true);
                } else {
                    empSearch.removeAttribute("disabled");
                }
            }

            modal.show();
        } catch (err) {
            console.error("openAddJobModal error:", err);
        }
    };

    // Edit Job
    window.openEditJobModal = function (job) {
        try {
            if (!job || typeof job !== "object") {
                console.error("openEditJobModal requires a job object");
                return;
            }

            const modal = showModal();
            if (!modal) return;

            const form = getEl("jobForm");

            // Atur route & method
            let actionUrl = form.dataset.routeUpdate.replace(":id", job.id);
            form.setAttribute("action", actionUrl);
            getEl("jobFormMethod").value = "PUT";

            // Isi field dari job
            getEl("jobTitle").value = job.title || "";
            getEl("jobGiverText").value = job.job_giver || "";
            getEl("jobTools").value = job.tools_and_materials || "";
            getEl("jobDescription").value = job.description || "";
            getEl("jobStartTime").value = job.start_time || "";
            getEl("jobEndTime").value = job.end_time || "";
            getEl("jobDepartment").value =
                job.department_target_id?.toString() || "";

            if (getEl("jobStatus")) getEl("jobStatus").value = job.status;
            if (getEl("jobStatusText"))
                getEl("jobStatusText").value = job.status;
            if (getEl("jobTicketNumber")) {
                getEl("jobTicketNumber").value = job.ticket_number ?? "";
            }

            // Bersihkan dulu chips lama
            form.querySelectorAll('input[name="employee_ids[]"]').forEach(
                (el) => el.remove()
            );
            getEl("selectedEmployees").innerHTML = "";

            // Prefill receivers (jika ada)
            if (Array.isArray(job.receivers)) {
                selectedEmployees = []; // reset dulu
                job.receivers.forEach((emp) => {
                    if (!selectedEmployees.find((e) => e.id === emp.id)) {
                        selectedEmployees.push(emp);
                    }
                });
                renderSelectedEmployees();
            }

            // Title & tombol
            getEl("jobModalTitle").innerText = "Edit Job";
            getEl("jobModalSubmit").innerText = "Perbarui";

            // Atur akses search
            let empSearch = getEl("employee_search");
            if (empSearch) {
                if (window.jobMode === "giver") {
                    empSearch.setAttribute("disabled", true);
                } else {
                    empSearch.removeAttribute("disabled");
                }
            }

            modal.show();
        } catch (err) {
            console.error("openEditJobModal error:", err);
        }
    };

    window.openJobModal = function (job) {
        const form = getEl("jobForm");

        console.log(job);
        // Atur route & method
        let actionUrl = form.dataset.routeUpdate.replace(":id", job.id);
        form.setAttribute("action", actionUrl);
        getEl("jobFormMethod").value = "PUT";

        // Isi field dari job
        getEl("jobTitle").value = job.title || "";
        getEl("jobGiverText").value = job.job_giver || "";
        getEl("jobTools").value = job.tools_and_materials || "";
        getEl("jobDescription").value = job.description || "";
        getEl("jobStartTime").value = job.start_time || "";
        getEl("jobEndTime").value = job.end_time || "";
        getEl("jobDepartment").value =
            job.department_target_id?.toString() || "";

        if (getEl("jobTicketNumber")) {
            getEl("jobTicketNumber").value = job.ticket_number ?? "";
        }
        if (getEl("jobStatus")) getEl("jobStatus").value = job.status;
        if (getEl("jobStatusText")) getEl("jobStatusText").value = job.status;
        // Bersihkan dulu chips lama
        form.querySelectorAll('input[name="employee_ids[]"]').forEach((el) =>
            el.remove()
        );
        getEl("selectedEmployees").innerHTML = "";

        // Prefill receivers (jika ada)
        window.selectedEmployees = []; // reset dulu
        if (Array.isArray(job.receivers)) {
            job.receivers.forEach((emp) => {
                if (!window.selectedEmployees.find((e) => e.id === emp.id)) {
                    window.selectedEmployees.push({
                        id: emp.id,
                        nik: emp.nik,
                        name: emp.name,
                    });
                }
            });
        }
        renderSelectedEmployees(true);

        // Title & tombol
        getEl("jobModalTitle").innerText = "Detail Job";
        getEl("jobModalSubmit").style.display = "none";
        getEl("jobModalCancel").innerText = "Kembali";

        // Atur akses search
        let empSearch = getEl("employee_search");
        if (empSearch) {
            if (window.jobMode === "view") {
                empSearch.setAttribute("disabled", true);
            } else {
                empSearch.removeAttribute("disabled");
            }
        }

        modal.show();
    };

    // Debug
    document.addEventListener("DOMContentLoaded", function () {
        if (!getEl("jobModal")) {
            console.warn(
                "jobModal not found in DOM — include <x-job-modal /> in your blade"
            );
        }
    });
})();
