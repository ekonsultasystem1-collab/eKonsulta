const initializeEkonsultaUi = () => {
    document.querySelectorAll('[data-resend-form]').forEach((form) => {
        const countdown = form.querySelector('[data-resend-countdown]');
        const button = form.querySelector('[data-resend-button]');

        if (!(countdown instanceof HTMLElement) || !(button instanceof HTMLButtonElement)) {
            return;
        }

        const availableAt = Number(countdown.dataset.countdownUntil) * 1000;
        let timer;

        if (!Number.isFinite(availableAt)) {
            button.disabled = false;
            countdown.textContent = '';
            return;
        }

        const updateCountdown = () => {
            const remainingSeconds = Math.max(0, Math.ceil((availableAt - Date.now()) / 1000));
            button.disabled = remainingSeconds > 0;
            countdown.textContent = remainingSeconds > 0
                ? `You can resend a code in ${Math.floor(remainingSeconds / 60)}:${String(remainingSeconds % 60).padStart(2, '0')}.`
                : 'You can request another code now.';

            if (remainingSeconds === 0) {
                window.clearInterval(timer);
            }
        };

        updateCountdown();
        timer = window.setInterval(updateCountdown, 1000);
    });

    document.querySelectorAll('[data-suffix-only]').forEach((field) => {
        if (!(field instanceof HTMLInputElement)) {
            return;
        }

        field.addEventListener('input', () => {
            field.value = field.value.replace(/[^\p{L}\p{M}\s.'-]/gu, '');
        });
    });

    const passwordResetForm = document.querySelector('[data-password-reset-form]');

    if (passwordResetForm instanceof HTMLFormElement) {
        const passwordInput = passwordResetForm.querySelector('[data-password-rule-input]');
        const confirmationInput = passwordResetForm.querySelector('[data-password-confirmation]');
        const matchStatus = passwordResetForm.querySelector('[data-password-match-status]');

        if (
            passwordInput instanceof HTMLInputElement
            && confirmationInput instanceof HTMLInputElement
            && matchStatus instanceof HTMLElement
        ) {
            const updatePasswordFeedback = () => {
                const password = passwordInput.value;
                const checks = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                };

                Object.entries(checks).forEach(([rule, passed]) => {
                    const item = passwordResetForm.querySelector(`[data-password-rule="${rule}"]`);

                    if (item instanceof HTMLElement) {
                        item.classList.toggle('is-met', passed);
                    }
                });

                const passwordsMatch = confirmationInput.value.length > 0
                    && password === confirmationInput.value;
                const hasConfirmation = confirmationInput.value.length > 0;

                matchStatus.textContent = !hasConfirmation
                    ? 'Re-enter your new password to confirm it.'
                    : passwordsMatch
                        ? 'Passwords match.'
                        : 'Passwords do not match.';
                matchStatus.classList.toggle('is-match', passwordsMatch);
                matchStatus.classList.toggle('is-mismatch', hasConfirmation && !passwordsMatch);
                confirmationInput.setAttribute('aria-invalid', String(hasConfirmation && !passwordsMatch));
            };

            passwordInput.addEventListener('input', updatePasswordFeedback);
            confirmationInput.addEventListener('input', updatePasswordFeedback);
            updatePasswordFeedback();
        }
    }

    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        if (!(toggle instanceof HTMLButtonElement)) {
            return;
        }

        const targetId = toggle.dataset.target;
        const input = targetId ? document.getElementById(targetId) : null;

        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        const showLabel = toggle.dataset.showLabel || 'Show password';
        const hideLabel = toggle.dataset.hideLabel || 'Hide password';
        const showIcon = toggle.querySelector('[data-password-icon="show"]');
        const hideIcon = toggle.querySelector('[data-password-icon="hide"]');

        const syncPasswordState = () => {
            const shouldShowPassword = input.type === 'password';

            input.type = shouldShowPassword ? 'text' : 'password';
            toggle.setAttribute('aria-label', shouldShowPassword ? hideLabel : showLabel);
            toggle.setAttribute('aria-pressed', shouldShowPassword ? 'true' : 'false');
            toggle.setAttribute('aria-expanded', shouldShowPassword ? 'true' : 'false');
            toggle.title = shouldShowPassword ? hideLabel : showLabel;

            if (showIcon instanceof SVGElement && hideIcon instanceof SVGElement) {
                showIcon.hidden = shouldShowPassword;
                hideIcon.hidden = !shouldShowPassword;
            }
        };

        toggle.addEventListener('click', () => {
            syncPasswordState();
            input.focus();
        });
    });

    document.querySelectorAll('[data-list-search-input]').forEach((input) => {
        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        const targetId = input.dataset.listSearchTarget;
        const list = targetId ? document.getElementById(targetId) : null;
        const status = input.closest('.list-search')?.querySelector('[data-list-search-status]');
        const serverSearchForm = input.closest('[data-server-search]');

        if (!(list instanceof HTMLElement) || !(status instanceof HTMLElement)) {
            console.error('List search is missing its target list or status message.');
            return;
        }

        const items = Array.from(list.querySelectorAll('[data-search-item]'));

        if (serverSearchForm instanceof HTMLFormElement) {
            let searchTimer;

            input.addEventListener('input', () => {
                window.clearTimeout(searchTimer);
                searchTimer = window.setTimeout(() => serverSearchForm.requestSubmit(), 350);
            });

            serverSearchForm.addEventListener('submit', () => window.clearTimeout(searchTimer));
            return;
        }

        const applySearch = () => {
            const query = input.value.trim().toLocaleLowerCase();
            let visibleCount = 0;

            items.forEach((item) => {
                const searchableText = item.textContent?.toLocaleLowerCase() || '';
                const matches = searchableText.includes(query);
                item.hidden = !matches;

                if (matches) {
                    visibleCount += 1;
                }
            });

            list.querySelectorAll('[data-search-group]').forEach((group) => {
                const hasVisibleItems = Array.from(
                    group.querySelectorAll('[data-search-item]')
                ).some((item) => !item.hidden);

                group.hidden = query.length > 0 && !hasVisibleItems;
            });

            status.hidden = query.length === 0 || visibleCount > 0;
        };

        input.addEventListener('input', applySearch);

        if (input.value.trim()) {
            applySearch();
        }
    });

    const userMenu = document.getElementById('userMenu');
    if (userMenu instanceof HTMLDetailsElement) {
        document.addEventListener('click', (event) => {
            if (!userMenu.contains(event.target)) {
                userMenu.open = false;
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                userMenu.open = false;
            }
        });
    }

    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    if (sidebar && menuToggle && sidebarBackdrop) {
        const setSidebarOpen = (open) => {
            sidebar.classList.toggle('open', open);
            menuToggle.setAttribute('aria-expanded', String(open));
            menuToggle.setAttribute(
                'aria-label',
                open ? 'Close navigation menu' : 'Open navigation menu'
            );
            sidebarBackdrop.setAttribute('aria-hidden', String(!open));
            document.body.classList.toggle('sidebar-open', open);
        };

        menuToggle.addEventListener('click', () => {
            setSidebarOpen(!sidebar.classList.contains('open'));
        });

        sidebarBackdrop.addEventListener('click', () => setSidebarOpen(false));

        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setSidebarOpen(false));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && sidebar.classList.contains('open')) {
                setSidebarOpen(false);
                menuToggle.focus();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 991 && sidebar.classList.contains('open')) {
                setSidebarOpen(false);
            }
        });
    }

    const imagePreviewModal = document.getElementById('imagePreviewModal');
    const imagePreviewImage = document.getElementById('imagePreviewModalImage');
    const imagePreviewCaption = document.getElementById('imagePreviewModalCaption');
    let imagePreviewTrigger = null;

    if (
        imagePreviewModal instanceof HTMLDialogElement
        && imagePreviewImage instanceof HTMLImageElement
        && imagePreviewCaption instanceof HTMLElement
    ) {
        document.addEventListener('click', (event) => {
            const target = event.target;
            const trigger = target instanceof Element
                ? target.closest('[data-image-preview]')
                : null;

            if (!(trigger instanceof HTMLElement) || !trigger.dataset.imagePreview) {
                return;
            }

            imagePreviewTrigger = trigger;
            imagePreviewImage.src = trigger.dataset.imagePreview;
            imagePreviewImage.alt = trigger.dataset.imageAlt || '';
            imagePreviewCaption.textContent = trigger.dataset.imageAlt || '';
            imagePreviewModal.showModal();
        });

        imagePreviewModal.querySelector('[data-image-preview-close]')?.addEventListener('click', () => {
            imagePreviewModal.close();
        });

        imagePreviewModal.addEventListener('click', (event) => {
            if (event.target === imagePreviewModal) {
                imagePreviewModal.close();
            }
        });

        imagePreviewModal.addEventListener('close', () => {
            imagePreviewImage.removeAttribute('src');
            imagePreviewCaption.textContent = '';
            imagePreviewTrigger?.focus();
            imagePreviewTrigger = null;
        });
    }

    document.querySelectorAll('input[type="file"][name="image"]').forEach((input) => {
        const preview = input.closest('.form-field')?.querySelector('[data-upload-preview]');
        const previewButton = preview?.querySelector('[data-image-preview]');
        const previewImage = previewButton?.querySelector('img');

        if (
            !(input instanceof HTMLInputElement)
            || !(preview instanceof HTMLElement)
            || !(previewButton instanceof HTMLElement)
            || !(previewImage instanceof HTMLImageElement)
        ) {
            return;
        }

        let objectUrl = null;

        input.addEventListener('change', () => {
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            }

            const file = input.files?.[0];
            if (!file) {
                preview.hidden = true;
                previewImage.removeAttribute('src');
                previewButton.dataset.imagePreview = '';
                return;
            }

            if (!file.type.startsWith('image/')) {
                preview.hidden = true;
                previewImage.removeAttribute('src');
                previewButton.dataset.imagePreview = '';
                return;
            }

            objectUrl = URL.createObjectURL(file);
            previewImage.src = objectUrl;
            previewButton.dataset.imagePreview = objectUrl;
            previewButton.dataset.imageAlt = file.name;
            previewImage.alt = file.name;
            preview.hidden = false;
        });

        window.addEventListener('beforeunload', () => {
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }
        }, { once: true });
    });

    const confirmationModal = document.getElementById('confirmModal');
    const confirmationIcon = document.getElementById('confirmIcon');
    const confirmationTitle = document.getElementById('confirmTitle');
    const confirmationMessage = document.getElementById('confirmMessage');
    const confirmationCancel = document.getElementById('confirmCancel');
    const confirmationOk = document.getElementById('confirmOk');
    let pendingConfirmationForm = null;
    let pendingConfirmationTrigger = null;
    const confirmedForms = new WeakSet();

    if (
        confirmationModal instanceof HTMLElement
        && confirmationIcon instanceof HTMLElement
        && confirmationTitle instanceof HTMLElement
        && confirmationMessage instanceof HTMLElement
        && confirmationCancel instanceof HTMLButtonElement
        && confirmationOk instanceof HTMLButtonElement
    ) {
        const closeConfirmation = () => {
            confirmationModal.hidden = true;
            document.body.classList.remove('confirmation-open');
            pendingConfirmationForm = null;
            confirmationModal.dataset.confirmKind = 'default';

            const appShell = document.querySelector('.app-shell');
            if (appShell instanceof HTMLElement) {
                appShell.inert = false;
                appShell.removeAttribute('aria-hidden');
            }

            if (pendingConfirmationTrigger instanceof HTMLElement) {
                pendingConfirmationTrigger.focus();
                pendingConfirmationTrigger = null;
            }
        };

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) {
                return;
            }

            if (confirmedForms.has(form)) {
                confirmedForms.delete(form);
                return;
            }

            event.preventDefault();
            pendingConfirmationForm = form;
            pendingConfirmationTrigger = event.submitter instanceof HTMLElement
                ? event.submitter
                : document.activeElement instanceof HTMLElement
                    ? document.activeElement
                    : null;
            confirmationTitle.textContent = form.dataset.confirmTitle || 'Are you sure?';
            confirmationMessage.textContent = form.dataset.confirm || 'Please confirm this action.';
            confirmationCancel.textContent = form.dataset.confirmCancel || 'Cancel';
            confirmationOk.textContent = form.dataset.confirmOk || 'Confirm';
            confirmationModal.dataset.confirmKind = form.dataset.confirmIcon || 'default';
            confirmationModal.hidden = false;
            document.body.classList.add('confirmation-open');
            const appShell = document.querySelector('.app-shell');
            if (appShell instanceof HTMLElement) {
                appShell.inert = true;
                appShell.setAttribute('aria-hidden', 'true');
            }
            confirmationCancel.focus();
        });

        confirmationCancel.addEventListener('click', closeConfirmation);
        confirmationModal.addEventListener('click', (event) => {
            if (event.target === confirmationModal) {
                closeConfirmation();
            }
        });

        confirmationOk.addEventListener('click', () => {
            if (!pendingConfirmationForm) {
                return;
            }
            const form = pendingConfirmationForm;
            confirmedForms.add(form);
            closeConfirmation();
            form.requestSubmit();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !confirmationModal.hidden) {
                closeConfirmation();
                return;
            }

            if (event.key !== 'Tab' || confirmationModal.hidden) {
                return;
            }

            const focusableElements = Array.from(
                confirmationModal.querySelectorAll('button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')
            ).filter((element) => element instanceof HTMLElement && element.getClientRects().length > 0);

            if (focusableElements.length === 0) {
                event.preventDefault();
                return;
            }

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            const activeElementIsInside = focusableElements.includes(document.activeElement);

            if (event.shiftKey && (document.activeElement === firstElement || !activeElementIsInside)) {
                event.preventDefault();
                lastElement.focus();
            } else if (!event.shiftKey && (document.activeElement === lastElement || !activeElementIsInside)) {
                event.preventDefault();
                firstElement.focus();
            }
        });
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeEkonsultaUi, { once: true });
} else {
    initializeEkonsultaUi();
}