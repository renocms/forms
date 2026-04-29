<template>
    <div class="admin-page">
        <div class="page-header no-bottom">
            <h1>{{ translate('forms_form_submissions') }}</h1>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ translate('forms_name') }}</th>
                        <th>{{ translate('forms_phone') }}</th>
                        <th>Email</th>
                        <th>{{ translate('forms_form_title') }}</th>
                        <th>{{ translate('forms_received_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="submission in submissions"
                        :key="submission.id"
                        class="clickable-row"
                        @click="goToSubmission(submission.id)"
                    >
                        <td>{{ submission.name || '-' }}</td>
                        <td>{{ submission.phone || '-' }}</td>
                        <td>{{ submission.email || '-' }}</td>
                        <td>{{ submission.form_title || '-' }}</td>
                        <td>{{ formatDate(submission.created_at) }}</td>
                        <td>
                            <div class="item-actions" @click.stop>
                                <span :title="translate('forms_view')" @click="goToSubmission(submission.id)">
                                    <Icon name="eye" :size="16" />
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="submissions.length === 0">
                        <td colspan="6">{{ translate('forms_no_data') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <PaginationControls
            :meta="meta"
            :loading="loading"
            @change-page="changePage"
        />
    </div>
</template>

<script>
import Icon from '@reno-cms/components/common/Icon';
import PaginationControls from '@reno-cms/components/common/PaginationControls';
import { getAdminPrefix, getFormSubmissions } from '../../api/forms';
import { t as formT } from '../../i18n';

export default {
    name: 'FormSubmissionsListPage',
    components: {
        Icon,
        PaginationControls,
    },
    data() {
        return {
            submissions: [],
            adminPrefix: getAdminPrefix(),
            loading: false,
            perPage: 20,
            meta: {
                current_page: 1,
                last_page: 1,
                per_page: 20,
                total: 0,
                from: null,
                to: null,
            },
        };
    },
    async mounted() {
        await this.loadSubmissions();
    },
    methods: {
        translate(key, params = {}) {
            return formT(key, params);
        },
        async loadSubmissions(page = 1) {
            this.loading = true;

            try {
                const response = await getFormSubmissions({
                    page,
                    per_page: this.perPage,
                });
                this.submissions = response.data || [];
                this.meta = {
                    ...this.meta,
                    ...(response.meta || {}),
                };
            } finally {
                this.loading = false;
            }
        },
        async changePage(page) {
            if (page < 1 || page > this.meta.last_page) {
                return;
            }

            await this.loadSubmissions(page);
        },
        goToSubmission(id) {
            this.$router.push(`/${this.adminPrefix}/forms/submissions/${id}`);
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
.clickable-row {
    cursor: pointer;
}
</style>
