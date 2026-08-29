# Project Structure

```text
fintrack-laravel/
├── pnpm-lock.yaml
├── debug_constants.php
├── composer.lock
├── architecture.md
├── phpunit.xml
├── phpstan-output.txt
├── phpstan.neon
├── output.log
├── README.md
├── pint.json
├── artisan
├── package.json
├── GIT_WORKFLOW.md
├── components.json
├── tsconfig.json
├── financial-tracker-direction.md
├── eslint.config.js
├── vite.config.ts
├── composer.json
├── pnpm-workspace.yaml
├── app/
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── FortifyServiceProvider.php
│   ├── Shared/
│   │   ├── Domain/
│   │   │   ├── DomainEventInterface.php
│   ├── Policies/
│   ├── Http/
│   │   ├── Middleware/
│   │   │   ├── HandleInertiaRequests.php
│   │   │   ├── HandleAppearance.php
│   │   ├── Resources/
│   │   ├── Requests/
│   │   │   ├── Settings/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── ConstantsController.php
│   │   │   ├── Settings/
│   ├── Actions/
│   │   ├── Fortify/
│   │   │   ├── ResetUserPassword.php
│   │   │   ├── CreateNewUser.php
│   ├── Modules/
│   │   ├── Transaction/
│   │   │   ├── Providers/
│   │   │   ├── Application/
│   │   │   ├── Infrastructure/
│   │   │   ├── Domain/
│   │   │   ├── Presentation/
│   │   ├── Ledger/
│   │   │   ├── Providers/
│   │   │   ├── Infrastructure/
│   │   ├── Category/
│   │   │   ├── Providers/
│   │   │   ├── Application/
│   │   │   ├── Infrastructure/
│   │   │   ├── Domain/
│   │   │   ├── Presentation/
│   │   ├── Transfer/
│   │   │   ├── Providers/
│   │   │   ├── Application/
│   │   │   ├── Infrastructure/
│   │   │   ├── Domain/
│   │   │   ├── Presentation/
│   │   ├── User/
│   │   │   ├── Providers/
│   │   │   ├── Infrastructure/
│   │   ├── Audit/
│   │   │   ├── Providers/
│   │   │   ├── Application/
│   │   │   ├── Infrastructure/
│   │   ├── Account/
│   │   │   ├── Providers/
│   │   │   ├── Application/
│   │   │   ├── Infrastructure/
│   │   │   ├── Domain/
│   │   │   ├── Presentation/
│   ├── Domain/
│   ├── Concerns/
│   │   ├── ProfileValidationRules.php
│   │   ├── PasswordValidationRules.php
│   ├── Console/
│   │   ├── Commands/
├── config/
│   ├── sanctum.php
│   ├── fortify.php
│   ├── auth.php
│   ├── app.php
│   ├── mail.php
│   ├── services.php
│   ├── database.php
│   ├── cache.php
│   ├── session.php
│   ├── queue.php
│   ├── inertia.php
│   ├── logging.php
│   ├── filesystems.php
├── resources/
│   ├── css/
│   │   ├── app.css
│   ├── js/
│   │   ├── app.tsx
│   │   ├── types/
│   │   │   ├── navigation.ts
│   │   │   ├── vite-env.d.ts
│   │   │   ├── global.d.ts
│   │   │   ├── ui.ts
│   │   │   ├── index.ts
│   │   │   ├── finance.ts
│   │   │   ├── auth.ts
│   │   ├── wayfinder/
│   │   │   ├── index.ts
│   │   ├── components/
│   │   │   ├── app-logo.tsx
│   │   │   ├── app-content.tsx
│   │   │   ├── user-info.tsx
│   │   │   ├── passkey-verify.tsx
│   │   │   ├── passkey-item.tsx
│   │   │   ├── text-link.tsx
│   │   │   ├── nav-main.tsx
│   │   │   ├── app-sidebar-header.tsx
│   │   │   ├── nav-footer.tsx
│   │   │   ├── breadcrumbs.tsx
│   │   │   ├── nav-user.tsx
│   │   │   ├── heading.tsx
│   │   │   ├── app-sidebar.tsx
│   │   │   ├── two-factor-recovery-codes.tsx
│   │   │   ├── password-input.tsx
│   │   │   ├── delete-user.tsx
│   │   │   ├── input-error.tsx
│   │   │   ├── passkey-register.tsx
│   │   │   ├── manage-passkeys.tsx
│   │   │   ├── alert-error.tsx
│   │   │   ├── appearance-tabs.tsx
│   │   │   ├── manage-two-factor.tsx
│   │   │   ├── app-logo-icon.tsx
│   │   │   ├── app-header.tsx
│   │   │   ├── user-menu-content.tsx
│   │   │   ├── app-shell.tsx
│   │   │   ├── two-factor-setup-modal.tsx
│   │   │   ├── ui/
│   │   │   ├── settings/
│   │   │   ├── transactions/
│   │   │   ├── accounts/
│   │   │   ├── categories/
│   │   ├── layouts/
│   │   │   ├── auth-layout.tsx
│   │   │   ├── app-layout.tsx
│   │   │   ├── settings/
│   │   │   ├── app/
│   │   │   ├── auth/
│   │   ├── hooks/
│   │   │   ├── use-mobile.tsx
│   │   │   ├── use-current-url.ts
│   │   │   ├── use-clipboard.ts
│   │   │   ├── use-flash-toast.ts
│   │   │   ├── use-initials.tsx
│   │   │   ├── use-mobile-navigation.ts
│   │   │   ├── use-two-factor-auth.ts
│   │   │   ├── use-appearance.tsx
│   │   ├── actions/
│   │   │   ├── App/
│   │   │   ├── Inertia/
│   │   │   ├── Illuminate/
│   │   │   ├── Laravel/
│   │   ├── lib/
│   │   │   ├── utils.ts
│   │   │   ├── axios.ts
│   │   ├── pages/
│   │   │   ├── dashboard.tsx
│   │   │   ├── welcome.tsx
│   │   │   ├── settings/
│   │   │   ├── auth/
│   │   │   ├── transactions/
│   │   │   ├── accounts/
│   │   │   ├── categories/
│   │   ├── routes/
│   │   │   ├── index.ts
│   │   │   ├── password/
│   │   │   ├── user-password/
│   │   │   ├── passkey/
│   │   │   ├── security/
│   │   │   ├── appearance/
│   │   │   ├── constants/
│   │   │   ├── danger-zone/
│   │   │   ├── transfers/
│   │   │   ├── sanctum/
│   │   │   ├── verification/
│   │   │   ├── transactions/
│   │   │   ├── register/
│   │   │   ├── well-known/
│   │   │   ├── profile/
│   │   │   ├── accounts/
│   │   │   ├── categories/
│   │   │   ├── two-factor/
│   │   │   ├── notifications/
│   │   │   ├── login/
│   ├── views/
│   │   ├── app.blade.php
├── routes/
│   ├── settings.php
│   ├── console.php
│   ├── web.php
│   ├── api.php
```
