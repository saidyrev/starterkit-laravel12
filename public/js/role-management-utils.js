// Role Management Utilities

// Auto-save draft function
function autoSaveDraft() {
    const formData = {
        name: $("#name").val(),
        display_name: $("#display_name").val(),
        description: $("#description").val(),
        permissions: $('input[name="permissions[]"]:checked')
            .map(function () {
                return $(this).val();
            })
            .get(),
    };

    localStorage.setItem("role_draft", JSON.stringify(formData));
    console.log("Draft saved:", formData);
}

// Load draft function
function loadDraft() {
    const draft = localStorage.getItem("role_draft");
    if (draft) {
        const data = JSON.parse(draft);
        $("#name").val(data.name);
        $("#display_name").val(data.display_name);
        $("#description").val(data.description);

        // Check permissions
        data.permissions.forEach(function (permissionId) {
            $("#permission_" + permissionId).prop("checked", true);
            $("#permission_" + permissionId)
                .closest(".permission-item")
                .addClass("selected");
        });

        return true;
    }
    return false;
}

// Clear draft function
function clearDraft() {
    localStorage.removeItem("role_draft");
    console.log("Draft cleared");
}

// Validate role name uniqueness
function validateRoleName(name, excludeId = null) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "/api/roles/validate-name",
            type: "POST",
            data: {
                name: name,
                exclude_id: excludeId,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                resolve(response.available);
            },
            error: function () {
                reject(false);
            },
        });
    });
}

// Permission search function
function searchPermissions(query) {
    const items = $(".permission-item");

    if (!query) {
        items.show();
        return;
    }

    items.each(function () {
        const text = $(this).text().toLowerCase();
        if (text.includes(query.toLowerCase())) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

// Export roles data
function exportToJSON() {
    const selectedIds = $(".role-checkbox:checked")
        .map(function () {
            return $(this).val();
        })
        .get();

    if (selectedIds.length === 0) {
        Swal.fire("Warning!", "Please select roles to export.", "warning");
        return;
    }

    $.ajax({
        url: "/roles/export/json",
        type: "POST",
        data: {
            role_ids: selectedIds,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            const blob = new Blob([JSON.stringify(response.data, null, 2)], {
                type: "application/json",
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "roles_export_" + new Date().getTime() + ".json";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        },
        error: function () {
            Swal.fire("Error!", "Failed to export roles.", "error");
        },
    });
}

// Import roles from JSON
function importFromJSON(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
        try {
            const data = JSON.parse(e.target.result);

            Swal.fire({
                title: "Import Roles",
                text: `Found ${data.length} roles. Do you want to import them?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, import!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/roles/import",
                        type: "POST",
                        data: {
                            roles: data,
                            _token: $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        success: function (response) {
                            Swal.fire("Imported!", response.message, "success");
                            location.reload();
                        },
                        error: function (xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire(
                                "Error!",
                                response?.message || "Import failed.",
                                "error"
                            );
                        },
                    });
                }
            });
        } catch (error) {
            Swal.fire("Error!", "Invalid JSON file.", "error");
        }
    };
    reader.readAsText(file);
}

// Initialize tooltips
function initializeTooltips() {
    $('[data-bs-toggle="tooltip"]').tooltip();
}

// Initialize role management page
function initializeRoleManagement() {
    // Auto-save draft every 30 seconds
    setInterval(autoSaveDraft, 30000);

    // Load draft on page load
    if (loadDraft()) {
        console.log("Draft loaded");
    }

    // Initialize tooltips
    initializeTooltips();

    // Add permission search
    const searchHtml = `
        <div class="mb-3">
            <input type="text" class="form-control" id="permissionSearch" placeholder="Search permissions...">
        </div>
    `;
    $("#permissionsContainer").before(searchHtml);

    $("#permissionSearch").on("input", function () {
        searchPermissions($(this).val());
    });
}

// Call initialization when document is ready
$(document).ready(function () {
    initializeRoleManagement();
});
