/**
 * QuizTv Admin Panel Helper Library
 * Binds safety double-confirms for deletion events, and handles CSV extension validation.
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {

        // 1. Intercept deletions for confirmation checks
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const confirmed = confirm('WARNING: Are you absolutely sure you want to delete this record? This action is completely permanent and will remove all associated database references.');
                if (!confirmed) {
                    e.preventDefault();
                }
            });
        });

        // 2. Validate CSV Import extensions before submissions
        const csvForm = document.querySelector('form[action*="import"]');
        if (csvForm) {
            csvForm.addEventListener('submit', (e) => {
                const csvInput = document.getElementById('csv_file');
                if (csvInput && csvInput.files.length > 0) {
                    const file = csvInput.files[0];
                    const extension = file.name.split('.').pop().toLowerCase();
                    if (extension !== 'csv') {
                        alert('ERROR: Only files with a .csv extension are allowed.');
                        e.preventDefault();
                    }
                }
            });
        }

    });
})();
