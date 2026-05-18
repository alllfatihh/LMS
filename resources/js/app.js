import './bootstrap';

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import Alpine from 'alpinejs';

window.Swal = Swal;

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('questionEditor', (init = {}) => ({
        template: init.template ?? '',
        formula: init.formula ?? '',
        variables:
            init.variables && init.variables.length
                ? init.variables
                : [
                      { name: 'x', min: 1, max: 10 },
                      { name: 'y', min: 1, max: 10 },
                  ],
        isLoading: false,
        hasPreview: false,
        previewTemplate: '',
        previewVariables: {},
        previewAnswer: null,
        previewError: null,

        addVariable() {
            this.variables.push({ name: '', min: 1, max: 10 });
        },

        removeVariable(index) {
            this.variables.splice(index, 1);
        },

        async runPreview() {
            this.isLoading = true;
            this.previewError = null;
            this.hasPreview = false;

            const rules = {};
            this.variables.forEach((v) => {
                if (v.name) {
                    rules[v.name] = { min: parseInt(v.min, 10), max: parseInt(v.max, 10) };
                }
            });

            const previewUrl = document.querySelector('meta[name="guru-soal-pratinjau-url"]')?.content;
            if (!previewUrl) {
                this.previewError = 'URL preview tidak tersedia.';
                this.isLoading = false;
                return;
            }

            try {
                const response = await fetch(previewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        question_template: this.template,
                        answer_formula: this.formula,
                        variable_rules: rules,
                    }),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.previewTemplate = data.parsed_template.replace(/\n/g, '<br>');
                    this.previewVariables = data.variables;
                    this.previewAnswer = data.answer;
                    this.hasPreview = true;
                } else {
                    this.previewError = data.message || 'Terjadi kesalahan saat memvalidasi rumus.';
                }
            } catch (error) {
                this.previewError = 'Koneksi ke server gagal atau terjadi kesalahan internal.';
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },
    }));
});

Alpine.start();
