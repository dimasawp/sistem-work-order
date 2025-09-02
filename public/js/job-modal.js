(function () {
    "use strict";

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

    // Tambah Job
    window.openAddJobModal = function () {
        try {
            const modal = showModal();
            if (!modal) return;

            getEl("jobModalTitle").innerText = "Tambah Job";

            const form = getEl("jobForm");
            form.setAttribute("action", form.dataset.routeStore); // route store
            getEl("jobFormMethod").value = "POST";

            // reset fields
            getEl("jobTitle").value = "";
            getEl("jobDescription").value = "";
            getEl("jobDepartment").value = "";
            if (getEl("jobStatus")) getEl("jobStatus").value = "pending";
            if (getEl("jobStatusText"))
                getEl("jobStatusText").value = "pending";
            getEl("jobModalSubmit").innerText = "Simpan";

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

            getEl("jobModalTitle").innerText = "Edit Job";

            const form = getEl("jobForm");

            // Action URL
            let routeTemplate = form.dataset.routeUpdate; // "/jobs/:id"
            let actionUrl = routeTemplate.replace(":id", job.id);
            form.setAttribute("action", actionUrl);

            // Ubah method jadi PUT
            getEl("jobFormMethod").value = "PUT";

            // Isi field sesuai fillable
            getEl("jobTitle").value = job.title || "";
            getEl("jobGiverText").value = job.job_giver || "";
            getEl("jobTools").value = job.tools_and_materials || "";
            getEl("jobDescription").value = job.description || "";
            getEl("jobStartTime").value = job.start_time || "";
            getEl("jobEndTime").value = job.end_time || "";
            getEl("jobDepartment").value =
                job.department_target_id?.toString() || "";
            if (getEl("jobStatus")) {
                getEl("jobStatus").value = job.status;
            }
            if (getEl("jobStatusText")) {
                getEl("jobStatusText").value = job.status;
            }
            // Tombol submit
            getEl("jobModalSubmit").innerText = "Update";

            modal.show();
        } catch (err) {
            console.error("openEditJobModal error:", err);
        }
    };

    // debug: pastikan modal ada saat DOM ready
    document.addEventListener("DOMContentLoaded", function () {
        if (!getEl("jobModal")) {
            console.warn(
                "jobModal not found in DOM — include <x-job-modal /> in your blade"
            );
        }
    });
})();
