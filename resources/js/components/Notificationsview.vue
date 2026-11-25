<template>
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">
                    Notifications
                </h2>
                <p class="text-sm text-slate-400">
                    Recent updates and alerts
                </p>
            </div>

            <span
                v-if="unreadCount > 0"
                class="px-2.5 py-1 rounded-full bg-red-500 text-white text-xs font-semibold"
            >
                {{ unreadCount }} unread
            </span>
        </div>

        <!-- Loading -->
        <div
            v-if="loading"
            class="rounded-lg border border-slate-800 bg-slate-900 p-6 text-center text-slate-400"
        >
            Loading notifications...
        </div>

        <!-- Empty -->
        <div
            v-else-if="notifications.length === 0"
            class="rounded-lg border border-slate-800 bg-slate-900 p-8 text-center"
        >
            <div class="text-slate-300 font-medium">
                No notifications
            </div>

            <div class="text-sm text-slate-500 mt-1">
                You're all caught up.
            </div>
        </div>

        <!-- Notification List -->
        <div v-else class="space-y-2">
            <div
                v-for="notification in notifications"
                :key="notification.id"
                class="rounded-lg border p-4 transition"
                :class="
                    notification.read_at
                        ? 'border-slate-800 bg-slate-900'
                        : 'border-indigo-500/30 bg-indigo-500/10'
                "
            >
                <div class="flex items-start gap-3">
                    <!-- Unread indicator -->
                    <span
                        class="mt-1.5 w-2 h-2 rounded-full shrink-0"
                        :class="
                            notification.read_at
                                ? 'bg-slate-600'
                                : 'bg-red-500'
                        "
                    ></span>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-4">
                            <h3 class="font-medium text-white">
                                {{ notification.title }}
                            </h3>

                            <span class="text-xs text-slate-500 whitespace-nowrap">
                                {{ formatDate(notification.created_at) }}
                            </span>
                        </div>

                        <p class="mt-1 text-sm text-slate-400">
                            {{ notification.message }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'NotificationsView',

    props: {
        notifications: {
            type: Array,
            default: () => [],
        },

        loading: {
            type: Boolean,
            default: false,
        },

        unreadCount: {
            type: Number,
            default: 0,
        },
    },

    methods: {
        formatDate(date) {
            if (!date) return '';

            return new Date(date).toLocaleString();
        },
    },
};
</script>