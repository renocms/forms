<template>
    <div class="admin-page">
        <div class="page-header">
            <h1>
                {{ submission ? `${translate('forms_submission_view')} #${submission.id}` : translate('forms_submission_view') }}
            </h1>
            <div class="header-actions">
                <button type="button" class="btn btn-secondary" @click="goToList">
                    {{ translate('forms_back') }}
                </button>
            </div>
        </div>

        <div v-if="submission" class="admin-table-container">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>{{ translate('forms_field') }}</th>
                    <th>{{ translate('forms_value') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ translate('forms_form') }}</td>
                    <td>{{ submission.form_title || '-' }}</td>
                </tr>
                <tr>
                    <td>{{ translate('forms_received_at') }}</td>
                    <td>{{ formatDate(submission.created_at) }}</td>
                </tr>
                <tr v-if="submission.consents?.length">
                    <td>{{ translate('forms_consents') }}</td>
                    <td>
                        <div v-for="consent in submission.consents" :key="consent.id">
                            {{ consent.title || '-' }}
                            <span v-if="consent.deleted_at" class="muted">
                                    ({{ translate('forms_revoked_at') }}: {{ formatDate(consent.deleted_at) }})
                                </span>
                        </div>
                    </td>
                </tr>
                <tr v-for="field in submission.fields || []" :key="field.key">
                    <td>{{ field.label }}</td>
                    <td>{{ field.value ?? '-' }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-if="submission" class="submission-details">

            <div class="details-section">
                <h4>Payload</h4>
                <pre>{{ formatPayload(submission.payload) }}</pre>
            </div>
        </div>

        <div v-else class="submission-details">
            <p class="muted">{{ translate('forms_submission_not_found') }}</p>
        </div>
    </div>
</template>

<script>
import { getAdminPrefix, getFormSubmission } from '../../api/forms';
import { t as formT } from '../../i18n';

export default {
    name: 'FormSubmissionShowPage',
    data() {
        return {
            submission: null,
            adminPrefix: getAdminPrefix(),
        };
    },
    async mounted() {
        await this.loadSubmission();
    },
    watch: {
        '$route.params.id': {
            async handler() {
                await this.loadSubmission();
            },
        },
    },
    methods: {
        translate(key, params = {}) {
            return formT(key, params);
        },
        goToList() {
            this.$router.push(`/${this.adminPrefix}/forms/submissions`);
        },
        async loadSubmission() {
            const id = this.getRouteSubmissionId();
            if (!id) {
                this.submission = null;
                return;
            }

            try {
                const response = await getFormSubmission(id);
                this.submission = response.data || null;
            } catch (error) {
                this.submission = null;
            }
        },
        getRouteSubmissionId() {
            const rawId = this.$route?.params?.id;
            if (!rawId) {
                return null;
            }

            const parsedId = Number.parseInt(String(rawId), 10);

            return Number.isInteger(parsedId) && parsedId > 0 ? parsedId : null;
        },
        formatPayload(payload) {
            return JSON.stringify(payload || {}, null, 2);
        },
        formatDate(value) {
            if (!value) {
                return '-';
            }

            return new Date(value).toLocaleString();
        },
    },
};
</script>

<style scoped>
.submission-details {
    margin-top: 1rem;
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #fff;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.5rem 1rem;
}

.details-section {
    margin-top: 1rem;
}

.muted {
    color: #6b7280;
}

pre {
    margin: 0;
    padding: 0.75rem;
    background: #f5f5f5;
    border-radius: 6px;
    overflow: auto;
}
</style>
