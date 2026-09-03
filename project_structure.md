# Project Structure

```text
fintrack-laravel/
├── .github/
│   ├── workflows/
│   │   └── tests.yml
│   └── dependabot.yml
├── app/
│   ├── Concerns/
│   │   ├── PasswordValidationRules.php
│   │   └── ProfileValidationRules.php
│   ├── Console/
│   │   └── Commands/
│   │       └── ProcessRecurringTransactionsCommand.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ConstantsController.php
│   │   │   ├── Controller.php
│   │   │   └── NotificationController.php
│   │   ├── Middleware/
│   │   │   ├── HandleAppearance.php
│   │   │   └── HandleInertiaRequests.php
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Modules/
│   │   ├── Account/
│   │   │   ├── Application/
│   │   │   │   ├── DTOs/
│   │   │   │   │   ├── AccountOutput.php
│   │   │   │   │   ├── CreateAccountInput.php
│   │   │   │   │   └── UpdateAccountInput.php
│   │   │   │   └── UseCases/
│   │   │   │       ├── CreateAccountUseCase.php
│   │   │   │       ├── DeleteAccountUseCase.php
│   │   │   │       ├── GetAccountsUseCase.php
│   │   │   │       └── UpdateAccountUseCase.php
│   │   │   ├── Domain/
│   │   │   │   ├── Entities/
│   │   │   │   │   └── Account.php
│   │   │   │   ├── Events/
│   │   │   │   ├── Exceptions/
│   │   │   │   ├── Repositories/
│   │   │   │   │   └── AccountRepositoryInterface.php
│   │   │   │   └── ValueObjects/
│   │   │   │       └── AccountId.php
│   │   │   ├── Infrastructure/
│   │   │   │   ├── Database/
│   │   │   │   │   └── Migrations/
│   │   │   │   ├── Persistence/
│   │   │   │   │   ├── Eloquent/
│   │   │   │   │   │   ├── Mappers/
│   │   │   │   │   │   │   └── AccountMapper.php
│   │   │   │   │   │   └── Models/
│   │   │   │   │   │       ├── AccountModel.php
│   │   │   │   │   │       ├── AccountTypeModel.php
│   │   │   │   │   │       ├── CurrencyModel.php
│   │   │   │   │   │       └── ProviderModel.php
│   │   │   │   │   └── Repositories/
│   │   │   │   │       └── EloquentAccountRepository.php
│   │   │   │   └── Services/
│   │   │   │       └── AccountBalanceService.php
│   │   │   ├── Presentation/
│   │   │   │   └── Http/
│   │   │   │       ├── Controllers/
│   │   │   │       │   └── AccountController.php
│   │   │   │       ├── Requests/
│   │   │   │       │   ├── StoreAccountHttpRequest.php
│   │   │   │       │   └── UpdateAccountHttpRequest.php
│   │   │   │       ├── Resources/
│   │   │   │       │   └── AccountResource.php
│   │   │   │       └── routes.php
│   │   │   └── Providers/
│   │   │       └── AccountServiceProvider.php
│   │   ├── Audit/
│   │   │   ├── Application/
│   │   │   │   ├── DTOs/
│   │   │   │   ├── Services/
│   │   │   │   │   └── AuditService.php
│   │   │   │   └── UseCases/
│   │   │   ├── Domain/
│   │   │   │   ├── Entities/
│   │   │   │   ├── Events/
│   │   │   │   ├── Exceptions/
│   │   │   │   ├── Repositories/
│   │   │   │   └── ValueObjects/
│   │   │   ├── Infrastructure/
│   │   │   │   ├── Database/
│   │   │   │   │   └── Migrations/
│   │   │   │   └── Persistence/
│   │   │   │       ├── Eloquent/
│   │   │   │       │   ├── Mappers/
│   │   │   │       │   └── Models/
│   │   │   │       │       └── AuditLogModel.php
│   │   │   │       └── Repositories/
│   │   │   ├── Presentation/
│   │   │   │   └── Http/
│   │   │   │       ├── Controllers/
│   │   │   │       ├── Requests/
│   │   │   │       └── Resources/
│   │   │   └── Providers/
│   │   │       └── AuditServiceProvider.php
│   │   ├── Category/
│   │   │   ├── Application/
│   │   │   │   ├── DTOs/
│   │   │   │   │   ├── CategoryOutput.php
│   │   │   │   │   ├── CreateCategoryInput.php
│   │   │   │   │   └── UpdateCategoryInput.php
│   │   │   │   └── UseCases/
│   │   │   │       ├── CreateCategoryUseCase.php
│   │   │   │       ├── DeleteCategoryUseCase.php
│   │   │   │       ├── GetCategoriesUseCase.php
│   │   │   │       └── UpdateCategoryUseCase.php
│   │   │   ├── Domain/
│   │   │   │   ├── Entities/
│   │   │   │   │   └── Category.php
│   │   │   │   ├── Events/
│   │   │   │   ├── Exceptions/
│   │   │   │   │   └── SystemCategoryModificationException.php
│   │   │   │   ├── Repositories/
│   │   │   │   │   └── CategoryRepositoryInterface.php
│   │   │   │   └── ValueObjects/
│   │   │   ├── Infrastructure/
│   │   │   │   ├── Database/
│   │   │   │   │   └── Migrations/
│   │   │   │   └── Persistence/
│   │   │   │       ├── Eloquent/
│   │   │   │       │   ├── Mappers/
│   │   │   │       │   │   └── CategoryMapper.php
│   │   │   │       │   └── Models/
│   │   │   │       │       ├── CategoryModel.php
│   │   │   │       │       └── CategoryTypeModel.php
│   │   │   │       └── Repositories/
│   │   │   │           └── EloquentCategoryRepository.php
│   │   │   ├── Presentation/
│   │   │   │   └── Http/
│   │   │   │       ├── Controllers/
│   │   │   │       │   └── CategoryController.php
│   │   │   │       ├── Requests/
│   │   │   │       │   ├── StoreCategoryHttpRequest.php
│   │   │   │       │   └── UpdateCategoryHttpRequest.php
│   │   │   │       ├── Resources/
│   │   │   │       │   └── CategoryResource.php
│   │   │   │       └── routes.php
│   │   │   └── Providers/
│   │   │       └── CategoryServiceProvider.php
│   │   ├── Ledger/
│   │   │   ├── Application/
│   │   │   │   ├── DTOs/
│   │   │   │   └── UseCases/
│   │   │   ├── Domain/
│   │   │   │   ├── Entities/
│   │   │   │   ├── Events/
│   │   │   │   ├── Exceptions/
│   │   │   │   ├── Repositories/
│   │   │   │   └── ValueObjects/
│   │   │   ├── Infrastructure/
│   │   │   │   ├── Database/
│   │   │   │   │   └── Migrations/
│   │   │   │   └── Persistence/
│   │   │   │       ├── Eloquent/
│   │   │   │       │   ├── Mappers/
│   │   │   │       │   └── Models/
│   │   │   │       │       └── LedgerEntryModel.php
│   │   │   │       └── Repositories/
│   │   │   ├── Presentation/
│   │   │   │   └── Http/
│   │   │   │       ├── Controllers/
│   │   │   │       ├── Requests/
│   │   │   │       └── Resources/
│   │   │   └── Providers/
│   │   │       └── LedgerServiceProvider.php
│   │   ├── Transaction/
│   │   │   ├── Application/
│   │   │   │   ├── DTOs/
│   │   │   │   │   ├── CreateRecurringTransactionInput.php
│   │   │   │   │   ├── CreateTransactionInput.php
│   │   │   │   │   ├── RecurringTransactionOutput.php
│   │   │   │   │   ├── TransactionOutput.php
│   │   │   │   │   ├── UpdateRecurringTransactionInput.php
│   │   │   │   │   └── UpdateTransactionInput.php
│   │   │   │   └── UseCases/
│   │   │   │       ├── CreateRecurringTransactionUseCase.php
│   │   │   │       ├── CreateTransactionUseCase.php
│   │   │   │       ├── DeleteRecurringTransactionUseCase.php
│   │   │   │       ├── DeleteTransactionUseCase.php
│   │   │   │       ├── GetRecurringTransactionsUseCase.php
│   │   │   │       ├── GetTransactionByIdUseCase.php
│   │   │   │       ├── GetTransactionsUseCase.php
│   │   │   │       ├── ProcessRecurringTransactionsUseCase.php
│   │   │   │       ├── ProcessSingleRecurringTransactionUseCase.php
│   │   │   │       ├── UpdateRecurringTransactionUseCase.php
│   │   │   │       └── UpdateTransactionUseCase.php
│   │   │   ├── Domain/
│   │   │   │   ├── Entities/
│   │   │   │   │   ├── RecurringTransaction.php
│   │   │   │   │   └── Transaction.php
│   │   │   │   ├── Events/
│   │   │   │   ├── Exceptions/
│   │   │   │   ├── Repositories/
│   │   │   │   │   ├── RecurringTransactionRepositoryInterface.php
│   │   │   │   │   └── TransactionRepositoryInterface.php
│   │   │   │   ├── Services/
│   │   │   │   │   └── HolidayServiceInterface.php
│   │   │   │   └── ValueObjects/
│   │   │   │       ├── ExecutionType.php
│   │   │   │       ├── RecurringFrequency.php
│   │   │   │       └── TransactionStatus.php
│   │   │   ├── Infrastructure/
│   │   │   │   ├── Database/
│   │   │   │   │   └── Migrations/
│   │   │   │   ├── Persistence/
│   │   │   │   │   ├── Eloquent/
│   │   │   │   │   │   ├── Mappers/
│   │   │   │   │   │   │   ├── RecurringTransactionMapper.php
│   │   │   │   │   │   │   └── TransactionMapper.php
│   │   │   │   │   │   └── Models/
│   │   │   │   │   │       ├── RecurringTransactionModel.php
│   │   │   │   │   │       ├── TransactionModel.php
│   │   │   │   │   │       └── TransactionTypeModel.php
│   │   │   │   │   └── Repositories/
│   │   │   │   │       ├── EloquentRecurringTransactionRepository.php
│   │   │   │   │       └── EloquentTransactionRepository.php
│   │   │   │   └── Services/
│   │   │   │       └── IndonesianHolidayService.php
│   │   │   ├── Presentation/
│   │   │   │   └── Http/
│   │   │   │       ├── Controllers/
│   │   │   │       │   ├── RecurringTransactionController.php
│   │   │   │       │   └── TransactionController.php
│   │   │   │       ├── Requests/
│   │   │   │       │   ├── StoreRecurringTransactionHttpRequest.php
│   │   │   │       │   ├── StoreTransactionHttpRequest.php
│   │   │   │       │   ├── UpdateRecurringTransactionHttpRequest.php
│   │   │   │       │   └── UpdateTransactionHttpRequest.php
│   │   │   │       ├── Resources/
│   │   │   │       │   ├── RecurringTransactionResource.php
│   │   │   │       │   └── TransactionResource.php
│   │   │   │       └── routes.php
│   │   │   └── Providers/
│   │   │       └── TransactionServiceProvider.php
│   │   ├── Transfer/
│   │   │   ├── Application/
│   │   │   │   ├── DTOs/
│   │   │   │   │   ├── CreateTransferInput.php
│   │   │   │   │   └── TransferOutput.php
│   │   │   │   └── UseCases/
│   │   │   │       ├── CreateTransferUseCase.php
│   │   │   │       ├── GetTransferByIdUseCase.php
│   │   │   │       └── GetTransfersUseCase.php
│   │   │   ├── Domain/
│   │   │   │   ├── Entities/
│   │   │   │   │   └── Transfer.php
│   │   │   │   ├── Events/
│   │   │   │   ├── Exceptions/
│   │   │   │   │   └── CrossUserTransferException.php
│   │   │   │   ├── Repositories/
│   │   │   │   │   └── TransferRepositoryInterface.php
│   │   │   │   └── ValueObjects/
│   │   │   ├── Infrastructure/
│   │   │   │   ├── Database/
│   │   │   │   │   └── Migrations/
│   │   │   │   └── Persistence/
│   │   │   │       ├── Eloquent/
│   │   │   │       │   ├── Mappers/
│   │   │   │       │   │   └── TransferMapper.php
│   │   │   │       │   └── Models/
│   │   │   │       │       └── TransferModel.php
│   │   │   │       └── Repositories/
│   │   │   │           └── EloquentTransferRepository.php
│   │   │   ├── Presentation/
│   │   │   │   └── Http/
│   │   │   │       ├── Controllers/
│   │   │   │       │   └── TransferController.php
│   │   │   │       ├── Requests/
│   │   │   │       │   └── StoreTransferHttpRequest.php
│   │   │   │       ├── Resources/
│   │   │   │       │   └── TransferResource.php
│   │   │   │       └── routes.php
│   │   │   └── Providers/
│   │   │       └── TransferServiceProvider.php
│   │   └── User/
│   │       ├── Application/
│   │       │   ├── DTOs/
│   │       │   └── UseCases/
│   │       │       └── Fortify/
│   │       │           ├── CreateNewUser.php
│   │       │           └── ResetUserPassword.php
│   │       ├── Domain/
│   │       │   ├── Entities/
│   │       │   ├── Events/
│   │       │   ├── Exceptions/
│   │       │   ├── Repositories/
│   │       │   └── ValueObjects/
│   │       ├── Infrastructure/
│   │       │   ├── Database/
│   │       │   │   └── Migrations/
│   │       │   └── Persistence/
│   │       │       ├── Eloquent/
│   │       │       │   ├── Mappers/
│   │       │       │   └── Models/
│   │       │       │       └── UserModel.php
│   │       │       └── Repositories/
│   │       ├── Presentation/
│   │       │   └── Http/
│   │       │       ├── Controllers/
│   │       │       │   └── Settings/
│   │       │       │       ├── ProfileController.php
│   │       │       │       └── SecurityController.php
│   │       │       ├── Requests/
│   │       │       │   └── Settings/
│   │       │       │       ├── PasswordUpdateRequest.php
│   │       │       │       ├── ProfileDeleteRequest.php
│   │       │       │       ├── ProfileUpdateRequest.php
│   │       │       │       └── TwoFactorAuthenticationRequest.php
│   │       │       └── Resources/
│   │       └── Providers/
│   │           └── UserServiceProvider.php
│   ├── Notifications/
│   │   └── RecurringTransactionExecutedNotification.php
│   ├── Policies/
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── FortifyServiceProvider.php
│   └── Shared/
│       └── Domain/
│           └── DomainEventInterface.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── broadcasting.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── fortify.php
│   ├── inertia.php
│   ├── logging.php
│   ├── mail.php
│   ├── pulse.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   └── session.php
├── database/
│   ├── factories/
│   │   ├── AccountFactory.php
│   │   ├── CategoryFactory.php
│   │   ├── TransactionFactory.php
│   │   ├── TransferFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_01_01_000000_create_passkeys_table.php
│   │   ├── 2025_08_14_170933_add_two_factor_columns_to_users_table.php
│   │   ├── 2026_08_25_093942_create_personal_access_tokens_table.php
│   │   ├── 2026_08_26_082808_create_accounts_table.php
│   │   ├── 2026_08_26_084835_create_categories_table.php
│   │   ├── 2026_08_26_090119_create_transactions_table.php
│   │   ├── 2026_08_26_090120_create_ledger_entries_table.php
│   │   ├── 2026_08_26_091757_create_transfers_table.php
│   │   ├── 2026_08_26_093143_create_audit_logs_table.php
│   │   ├── 2026_08_29_100424_create_constants_tables.php
│   │   ├── 2026_08_29_110532_add_account_number_to_accounts_table.php
│   │   ├── 2026_08_29_122500_add_parent_id_to_categories_table.php
│   │   ├── 2026_08_29_152822_create_providers_table.php
│   │   ├── 2026_08_29_152823_add_provider_id_to_accounts_table.php
│   │   ├── 2026_08_29_163820_add_debt_and_receivable_to_account_types.php
│   │   ├── 2026_08_31_141155_create_recurring_transactions_table.php
│   │   ├── 2026_09_01_021538_create_notifications_table.php
│   │   └── 2026_09_01_025152_create_pulse_tables.php
│   ├── seeders/
│   │   ├── ConstantsSeeder.php
│   │   ├── DatabaseSeeder.php
│   │   └── ProviderSeeder.php
│   ├── .gitignore
│   └── database.sqlite
├── graphify-out/
│   ├── cache/
│   │   ├── ast/
│   │   │   └── v0.9.38/
│   │   │       ├── 0098616962ebe62933359bdc7c3211e2391666853d3850fadf86cffd9b1b6149.json
│   │   │       ├── 00ce8af0c84b6f53a56a039632747ada723764c705a817d4ed68506db6692be4.json
│   │   │       ├── 031cdca273390459d746f67f473abdac136d7845c2147e8d79db9400561ccc29.json
│   │   │       ├── 05c4d08076f4b0a804d475522d5516a294e21a46aa171020e9ef8048f0d48fd1.json
│   │   │       ├── 069c99bb4442aaf50b3a96e95c0d6c1ec4da2b7d7ac1f55a23fd4d764e56bee4.json
│   │   │       ├── 079b3e62c3fb9473b118a4963760f864ce37f2a1fac2aff199bba76a3257c3ce.json
│   │   │       ├── 0801d26966e6f7afa519e8a6245b6840a17c06123e63f5d096ac297bfbe5cea2.json
│   │   │       ├── 0860a2f102764c11af9d6e29aa17373aaac2bc74def689c377d6c00be2242d14.json
│   │   │       ├── 08d30ce24a42a287cd3134f1faa09162b6d859ec32bc6783dd5d50f1175573ee.json
│   │   │       ├── 09188d629c6397a39719109f51b680fb4ee3a45bc0f81017d4a62139dce6d18d.json
│   │   │       ├── 0ab9ee9dee398628158d71e04433b1e27cbb2d845486253cbe58cb0d91a36c23.json
│   │   │       ├── 0c748bffbed362367febb4d14ea7396f277af82db96688a832e15453c8f755cc.json
│   │   │       ├── 0e4ecc4736087fabf1ee515c4b8ebc9ab99b8f1639fbd5879c899945e21476ae.json
│   │   │       ├── 11378592bc0f89cc35f03ac7edfab45d1e9f8c919960e4bb66b34881c58a9567.json
│   │   │       ├── 11c0689df9da7ef090af8db196f10155a638165bbd3f8b3c4b7813d24ca49a95.json
│   │   │       ├── 12ee47e8aa768aec4144a24e3708ea241538bd1a19209646a62007c66ce82dac.json
│   │   │       ├── 139eda6405f7b4fbc5a413b1b52927d8cda7b30fc1c1d5afc85a0a4b103a0b76.json
│   │   │       ├── 142e28deae7a9d715f354b3b4bdd70f94c95f9b0bad45128a81c6ae7ac8d08f6.json
│   │   │       ├── 1516c8d8eb3430593accacdec15e35d791280e8f06eea8596da4a97bf67fbed2.json
│   │   │       ├── 152cf702be1580812f1f66df37e6eebc1785f5154d65093e6b43b32b5c168be5.json
│   │   │       ├── 1565f1424e4aa72eed115f97351fff250bca4e2bb31d6d9e2da8d17a23b1db7c.json
│   │   │       ├── 17eed95abd1f6e9e7d289c0fb2a5c86e8e9d789ccf9f38855fb8063bdcebdb39.json
│   │   │       ├── 1aafa33c96d611968a29d37ea3fb6ce082c001b6723e18ee9f75dd751ac4834d.json
│   │   │       ├── 1d0233be9282ae0fb777550f07142847220ad6a162f3c6725bf12063bea2c60a.json
│   │   │       ├── 1fa5cb6b186746e642f1a8f4b32fc9bad8f1281522e94dac7c9db3bc5d0775cb.json
│   │   │       ├── 20487c17faff59b038a3652be50241c6fb495c1c2cd47f016018807558c61fdb.json
│   │   │       ├── 221cf80b29192b854a62068378ab6a382baab163fbbb640bdf940682cc324bfa.json
│   │   │       ├── 235cb2c1a3a5842e45f981b0013f684378d99f0e639199492694f01e51c2a73b.json
│   │   │       ├── 23c701233c51aba27713e5cc98f31946ef0afa24ca9d20f14931cd96258cd4b7.json
│   │   │       ├── 2461a6232f8e46c0ec1e9e1de615bd5451cf740866d35f634f19dff3408b8924.json
│   │   │       ├── 292306fdafd7ea513ba17eaef5f7f6829814ffa3f90488fedd1bf82cdf9e4411.json
│   │   │       ├── 296e083cd2466fd3ccde60e46998b0c9bc9ea244379ba89cd7a608bb4bc5d129.json
│   │   │       ├── 2a2c42c09c8039226090009242bc98a888153d3ca8f5e977ce8154ecf9fa4497.json
│   │   │       ├── 2b8f93dd6dc6fa72cec390a68c0c9278079b193f5fc177b119682b4e0f902bd6.json
│   │   │       ├── 2c63013801d8a86fdf58fa9ee8af2c5fff7de71a5de4a93a31efcc8e4b18a9d8.json
│   │   │       ├── 2d646efaab0f7d3a43ea88fd272f530d25411972666d0cf06fc4a6148e0ac0e6.json
│   │   │       ├── 2e3f40315a9edc71016fa9c2fe95155bb24d74c549facc25657a8fb1de88f2f9.json
│   │   │       ├── 2e3ff2527565e9cac89575a0ba01f79c3a5548b95095cf8cc42d4f70b1e327b8.json
│   │   │       ├── 3123799dc63dd2bb944fe7e095eff5d15474b1a2dae945a47f2c10d126b1b964.json
│   │   │       ├── 3182686623634c651c09317153f80871dd26543d8d66e6323fe46acc2564e56e.json
│   │   │       ├── 3244338922ff433a6b5aff708104671f0de58a800afc6ef287cb85201fe598b7.json
│   │   │       ├── 326fbd50428c2029f44473467ff29b5c3a36955cadda8db5d55130930efead58.json
│   │   │       ├── 33a3e1198a92e2039b39633f3669c116ae21af31cfc34b8defd53d317be3ad2a.json
│   │   │       ├── 34722e64efe12043c563329da903b8f55aa79594609a07cf53aa2b2c22a410ea.json
│   │   │       ├── 357d53ada57515a5b86f1d79a8e57100503ca130ae725c1326241aaf53027561.json
│   │   │       ├── 35c2c826ddad10cd5e0be86bfc35e3aa94e33e14c3c1ecdfc2239d765398f3c6.json
│   │   │       ├── 36df9efed0f43571f4b5804febd7f96dabea782b9b191ec3b5ed1513c32e415d.json
│   │   │       ├── 36f90028bcf665e294e4018a49d7987219f00982070c00427334c9802c592adf.json
│   │   │       ├── 36fef601683029a5fe8d71c1ebadcdd630be4dfad554ebd2ca99b864ef65f074.json
│   │   │       ├── 37a26dc8e7231bb7cd601888a51f8bafd76f4dc50d185d1ab0bca6372abb61a3.json
│   │   │       ├── 37eb9b5e15ebb7dea3b88d7aa8999ac36c6a3f14fcd64c1f24f3b3baf9e86f13.json
│   │   │       ├── 3a937107795dbb639be1aff8c32d9e246d91864f75b236b1eceda6503c3b3e1d.json
│   │   │       ├── 3b17c904dd7d7c8052346ae0d1490e6d07da3866672b164c78459c39333edebf.json
│   │   │       ├── 3d991e2a8a86a91f4cccf48692605ff6b99404d1509e23153dcf98454bc744c5.json
│   │   │       ├── 3ed2befcbe357ecaa6f7d5e1df4920c32f0ec50f700cd285dd147a6b834bb7e4.json
│   │   │       ├── 3f6513b2341baa15c419e80931ca36ed60898dd96405b07f83c7b7c2207a30eb.json
│   │   │       ├── 405063b293cb6e0dc27c847ab995b9c0ae72e94d48adff278b15d552d8799338.json
│   │   │       ├── 41685ca18b77e97d2140c3e23db9c44d0fba6e42a39fa79f6ef011df9eed17c4.json
│   │   │       ├── 423e5613ef640e722a287acb5bd6a1bd6189934a128125218f01b191d9f0df4b.json
│   │   │       ├── 435647a62e5326154eebb6f29574b05287bd8674827eab28b17bdd2c8f516cbe.json
│   │   │       ├── 43ebb415fb987b008e28f7428051b8475afd08ee35c6d220bcb43953e0a84a02.json
│   │   │       ├── 447e557988a04f7895486f5b06025eed8a663e73d8be11061fe3ac65f6869c3e.json
│   │   │       ├── 47c9e8ff6d1578432d2954de9b8ed765e2291029e601f191fd61ff3265d149f3.json
│   │   │       ├── 47de810a1b204b3ed416348ab7faf14d8f1bd82a247cd9facd90c2a1889095b5.json
│   │   │       ├── 49e52b557a1faac616cb5e2bd0af92837e58383d462ae48ce2bdd6ec93b15418.json
│   │   │       ├── 4a7b116ace3d4b1919c4e072f846ec9ef7ad6f1cdc783ed064c854a70f9e6b31.json
│   │   │       ├── 4cb5ab7304ca8ff306917ae5dfa424cbcfc04f4ef9a1755ec9b639cb1f4a817c.json
│   │   │       ├── 4cece662985e61c4a56cfa3be4f8282ff35a4dfaf4dfe9d2908b997751bc1708.json
│   │   │       ├── 4df1758d0b1c1206baf6e5c941fb70526560d36cbd5b1a322f1082ff3b3a5a29.json
│   │   │       ├── 4f0074af92c77637689afeda16283438a03c13e830df1965d8c7ed4758454b31.json
│   │   │       ├── 50b5a35be6de483752786032212be484998e47f4e07bc0f7da47ee9002d990c3.json
│   │   │       ├── 51250f88bdf37cbfa9846a583c939a67ad1759011f0bab25a0afce37bd13d970.json
│   │   │       ├── 516b19c1bd9e8d712e32d62e8af28c344ebbc7e8483c52f6166ed4d5bc4fc738.json
│   │   │       ├── 51bb17f288bea2571f76ef5c403d338e80be93441e420ba9817037895036d27d.json
│   │   │       ├── 5385795fabdc62099aadcb9a3fb92b457ca532d31c9f632657880015f063f145.json
│   │   │       ├── 592f09f415e99c449aa0f7ade67988e48d707c7b4f8aeeb93121f2f2ee9b63c8.json
│   │   │       ├── 5a63937720ef258c5e6a8f69c94f378542bc0738b3c61b78369582b41af2d478.json
│   │   │       ├── 5a7fb013da9871f5a5cd7356413f170b15d8e1b6323b4a3167f395432ad1367a.json
│   │   │       ├── 5a9d7223d0a5cbc03e17af2fd3f09baab9735a33917b58c95f141e6f73d4bbed.json
│   │   │       ├── 5b02c9bb4ed06cbe186f5f5d2010b47958de530539d0cb9f52dc086db76a76ee.json
│   │   │       ├── 5ce93e68778c5040a615ca164459e6c4dfe9e8a3248d15098600f3e42c01de61.json
│   │   │       ├── 5e0aadc890661513d4e1b2535e25d6ec6bfaf4e23461fa85e1550cb8d89e661d.json
│   │   │       ├── 603edc9bb3fb0091c77e3274b6175dfd3bd6faf84b122401c70a162bbf21bf6f.json
│   │   │       ├── 61426476339597b8ae9c3d4d8c7671950d1e5786b482b086418aeff28aeae3c1.json
│   │   │       ├── 62e9539e14a243d22889e745d55e48bc6c44de76f3c3b8b449539f6857e58fde.json
│   │   │       ├── 63e30f2a34921cc01093e4cefbead07d72eb2fedd641abde41c004f8c22cf828.json
│   │   │       ├── 6525b4dcfe566447a89f7a0e74659e7bdbce98dd0259c80ef4055d575cd7c6f9.json
│   │   │       ├── 661d7faacb9eaf509a42a2bc18473671829bd2b2781c861a48c8b75354eceba0.json
│   │   │       ├── 68175c2247a2fa1998225eedbf13ddb840e20fbe9c9f11e5f4f0d491238d2266.json
│   │   │       ├── 6a0fd43647189ecb69e45601c6187240aa798c6d1923bb11fb5ded3ed55f9531.json
│   │   │       ├── 6a781a32380c68c2810da96db050c2924bd02a959c866602f2dab4c6560be956.json
│   │   │       ├── 6af65c2e87ee28d399a69fd7da838d7f37c2350cfea8bf9871d6fd9b0c104d45.json
│   │   │       ├── 6af793415f994c2d9f00eef6d62ba340e431f26c33f6bd94ef1297c04e154d81.json
│   │   │       ├── 6ce2b3a469d9b99f979e9e1f357c97b96b6f06b013fa1eef2e95f6ff601c5b2d.json
│   │   │       ├── 6d39d7b86ce186b7fc8c848c8b49a8c246ebe22a6413e9444d6b36829240ef7a.json
│   │   │       ├── 6daa00b91af6a73fee6083302c8eb16f641e0c63f405ad9331a901581c634727.json
│   │   │       ├── 702b3aac6f7ef48bd464db3fa8e2c2626707be2d8ec0488b35c595fac5598c0a.json
│   │   │       ├── 7233ec26ec61c5cfca6168761c0abd1b510e98324beb339d92e84e9c78d197c9.json
│   │   │       ├── 72a867d0f146dbf1007502ff7527c17090dc40e0c10694c9b528870843f173ab.json
│   │   │       ├── 78c8be4b9488cf6b9533abc7c14f0d448d0a1c71cff5ccf330657377714ebdce.json
│   │   │       ├── 7b0aa9eb05929b80772bb84e4e898fdcae637ea421e35fd0780cb3ed7d99fecb.json
│   │   │       ├── 7b39fdb00697bace0f6f647e16549dabffab5afeb7734dc75e51a4620c01e889.json
│   │   │       ├── 7be1184cc6b369f913bc413d3196b39361316066a83c482fe52b53d1e496ef04.json
│   │   │       ├── 7c02369c7d6d62f23d3e6a6f9b0685a234e09299752be9e34391bead7c40fd42.json
│   │   │       ├── 7c9c0eccbab14900c24faa8fa0f4c547ef0c8bb3b4951f8a9910375504168dc9.json
│   │   │       ├── 7f665ecc661b68f5590fa5b953d5c801dd5bc539c44fea421f07d2eacb49a68a.json
│   │   │       ├── 7f876b68dbe1d9e0fc92341739eff4a8fcbf10b481a4f49f0917bc1128fc00df.json
│   │   │       ├── 7f9ca4aa8c3b38b3bbc53749bc2a2bd69bbb08bd125050d5e795581e06d74213.json
│   │   │       ├── 80cd1dfb1e0e33850802a3debd5726b1c8f19784462dd1d49aa609b6866532da.json
│   │   │       ├── 82a5ab0178469cec7d54940f38bff5ed4257b1ac1d54104191614d19d8ccd981.json
│   │   │       ├── 8314f98de456bb7e5c6dae1d8aaf2e8e9318e33d6e6db462b99026cad9331d07.json
│   │   │       ├── 842f6bd557490db9ac67806d9d75714a4ca2ae70a1d406aacf1bd36849aa3a2c.json
│   │   │       ├── 849b52d9169d5fa2fb5b9a78eae6e413d8d6bcd31b39187f7a7cd2d22ef88141.json
│   │   │       ├── 852c43933e51f36248c6933869cf732c52854d63670a1d34a60357367f199748.json
│   │   │       ├── 87808f34827b2a9f0c575082d8c24eec9bd3470ec4cf49928650b66bc0502489.json
│   │   │       ├── 88493592e53c159c64b3d9f3f8d8c64a5433814dd3bce8c141d2a27b7cf341cf.json
│   │   │       ├── 89577987502272ddc845da1c1d7211a55f47e82ba2631be1ac3dfba6e350603b.json
│   │   │       ├── 89cba8c71075272436150d23c3893fe79f1b9d4909e6eedfb3475ff30f92af1c.json
│   │   │       ├── 8a1292a850fa6df64ef1618b4acb04e25fb894fcf9ddba4051cd7fc5fe1e4381.json
│   │   │       ├── 8a850aa80a990dcf6258dfd2d766fdf09e8865b542b8de1cda9faa449078d9ae.json
│   │   │       ├── 8b2c9fa13f484c473792187fe663f6a89f67a7c3c9af1206769d4d1a1603c437.json
│   │   │       ├── 8d051374462433ea153d91cb881a16aec227ab3575736ee147ec99fb3c53bc30.json
│   │   │       ├── 8e1e6afc162f28aa8788739ddda963f9054efc865361759286ecd3de720666fb.json
│   │   │       ├── 8ec1de32a6810e74a1008d46267b4d334d22b4a8cbaf665f2da8104e49064c65.json
│   │   │       ├── 8f9ec3fd318e011b4285a49c1c5f850fd88a77d7addab6df59317b84aa7fcde2.json
│   │   │       ├── 8ff0367fdbde77ff45c26c648558d4dcfa4cd40bde45cd6c42cd559d39b03b77.json
│   │   │       ├── 96a7e783e3e604ab8ad56cbe26437faa45c36452298af027e5d7aa341006f3b1.json
│   │   │       ├── 97ff901d22d98a32a4bc2ea2c7d2120f3a1beb6994e1108243784251872f7de5.json
│   │   │       ├── 99738744338708e82e0e9668271ddf816b8ec51c4416c0e0971b46bb1a1efced.json
│   │   │       ├── 99a8c640f1c083277db5f14222fca99e9d1b48f5ee1fb77d040209e457cfb6bf.json
│   │   │       ├── 9afb90b9272a72900cd463b9bf34ae1409b8b06cac01c0653c04b67f8699a638.json
│   │   │       ├── 9d9dbcb92d979875bbb0f6dcee5caeaaa1735034437e16361105d009118ca5b2.json
│   │   │       ├── 9f33cc142c4ed3c92dcf7f44b99426177011b67e8b359bcb9959a88e740821e1.json
│   │   │       ├── 9f9f736331819893e37b33948ede53f85d32e3bb4c890e9896596c7a9d0a7cfc.json
│   │   │       ├── a0783a4b1dcbd5e2b066e082bc85fc1fba2a219474c4f3e8f27273b2f189167e.json
│   │   │       ├── a0e2e37aedd026c95afd3ed256551996e3aa697ee5055fc42e8314fd5ad6db78.json
│   │   │       ├── a1a3454787f0de8f042faaa83a9f083130809365fca70cfa75d25aa2a27978d6.json
│   │   │       ├── a2ad5622e25b9f41a2a534a3a261b45403b16015a87a4d6dc38c611aeecc7b0a.json
│   │   │       ├── a32e6a8080df0a9cfefe6cd6c8dac621aff6181938e03b7a8423c9ae7ad6a709.json
│   │   │       ├── a484f03f42a43826df476f0912c72f0f59a0dd74f20894993a2009082e8d27c8.json
│   │   │       ├── a49598ecd32fc2308bbbe38c82409b4d449c27a2b51db0ad0720b2a9539bc5e6.json
│   │   │       ├── a637fc9ff36c2a7fb694b5d2dae0c3af3c407b6b2bb6fe15b3272ef05de067f1.json
│   │   │       ├── a67c20eaffc4f805f9d1caabbb8c90812c1468fb91070c05f76475068802e7e2.json
│   │   │       ├── a6cc60e8f2638c354b44eace5695b2fd169f783b968c8d3ec97f14190d442f1c.json
│   │   │       ├── a73988384d4dd8cdec8e657f120311236c8ab120184dc3bea117201a691ec935.json
│   │   │       ├── a86e18c278b39e60c3b6f91b9f966c9d3fc90d121cacd3283885a94bde405efd.json
│   │   │       ├── a9ca462435e5448eb58ede285f5c79032d21262140dba0868310b0c5c087bf83.json
│   │   │       ├── aa0ccce84c9a3534f4176792383f0fd360bcde13c6d6d21ef18799a80ce07a3d.json
│   │   │       ├── ab011b0594e90a9638fbe4ca87cb60339effdd84f356bb5569b12e3635c2f8a3.json
│   │   │       ├── ab7048e5b9f7cc346edc8964249a024479b8cd6819f395785c13c5aa818fa8f9.json
│   │   │       ├── ad792b108265ecf11ef6dd8af5c90a541495bd6bac52ce580fd12396f3225899.json
│   │   │       ├── b1bfb3cc6b09bcc3f629ff9629238276b306f6d195542db7833c836134f80a58.json
│   │   │       ├── b204283ab9eebf28751dc9be2a90e03f5161bf2692155c42d4a6f0de9804098b.json
│   │   │       ├── b23a3215de26972fb4040c881022505fbf49c7bfc2a72eec3b6ea53096ed364f.json
│   │   │       ├── b3e9d5189ef1dd70422bbfb061fc198dedeedf92c656b971eaac0732fc5ec051.json
│   │   │       ├── b4680cdf46343fa8cbaa19dd34929040aa4f04a24ec631e71b72cca316a7a964.json
│   │   │       ├── b829636989939885889c95a1529d4632538bca6a23f02abaedd35c2b7be7868c.json
│   │   │       ├── b86ba44061411d24e76034a44ffe8565f23b34b4a682d7ea14a99a9fc4187374.json
│   │   │       ├── b8cad34943775b2823f05ce21055f2b112eb91ebc155e5f3c5b3a6eadb37bbc0.json
│   │   │       ├── b8f7becf81132fea0bbe5b48e4f8f9cae844ed8172f2b353637e765ca8f398b2.json
│   │   │       ├── ba00a7a2c22bff8a9b17a04f710dc651cc4cb990e2f15eda065e66fda083513a.json
│   │   │       ├── bbffe9b5679590b12428c04380f84a5ac578ea0383141743e4f688395ec52d9e.json
│   │   │       ├── bcf84ec7e2a73c6b9656d306fa3985a10a22d356e815480caf98878c0172864a.json
│   │   │       ├── bf6eeac0f217f827e8b376ec0c7a27a9d06f00de8e6fb4a1698885cf0d484a6b.json
│   │   │       ├── c1b3b71a09709d6c3cddda6ce90b484c4a2c0126d6dd7617148697b653feeef8.json
│   │   │       ├── c24b3a24dc4fc27137ac06910389e28df0443495b7faac59b64ac26248c1f948.json
│   │   │       ├── c35c7307b33296adcf871e030dc95ca1d919318ad1df6f99d3ece628208bcf41.json
│   │   │       ├── c3c818152c215029f8a8c9136454a4a500e55c2e40a14164593e192037d1bf7b.json
│   │   │       ├── c4fcb3b111ed4fb52878fbb516c4f7b4fb62882e1ef3c202a76e03ea057ba3ec.json
│   │   │       ├── c5761fe01f93dbc1d9b2590c87e871d169e2fd0394dae9f83dc10368698b9546.json
│   │   │       ├── c6df6e2116c36922a630bb70d8d087d6b8f746e773cf045725496a0d6a3bcafc.json
│   │   │       ├── c7f0676ad402be0e6d578d371c32a3158b8df2d2d11239a480fdd1d960ade807.json
│   │   │       ├── c86f9843059eae1ebb6c645a7fd65d2653966bc6a1efd8ecb8859e3ea9c724da.json
│   │   │       ├── c8c713fa56f620618a796fd3d757c5ebca8aa498bdbecb14eb037942cf07622d.json
│   │   │       ├── c8ec3c44b5e41d49a88ebd0f65b29b87e7e3187452a8df92cd01e0c6444fb08f.json
│   │   │       ├── ca154869201cd0333d87fe749ab35151d7cfc371e965ecd6de621fa1d96993e2.json
│   │   │       ├── cd0c7f61960c55fae53b2e22fc7fd26bc0e4c40b7a57afbd50fae4084b1be235.json
│   │   │       ├── cdb160e64bdfc8a514542eeac69a72c4c21e025af7bd8791b2d98d56ede7b315.json
│   │   │       ├── cdf0bd4bcbf25dc5412757209f6e6380a38e16169e2b51d1aed64a12e0b4ad8f.json
│   │   │       ├── cee873823ca692031c4679d3e70cc01d9799dcb4b9e419befcaafa5ea8424e8c.json
│   │   │       ├── d05a7785dddcca430e663398d0bba41c6f59db3ad77459afde4ab988849aa4a5.json
│   │   │       ├── d3bae604cb0a7edf18ce76c557b320aa7380ccdd081634fd0caa6e6d846cf6c0.json
│   │   │       ├── d6f7b5cf8ef0b3db3c6576821bf527535043eee51c151a6833d411da8a443cca.json
│   │   │       ├── d8280806db06b39acb1d06d3fdceb4e29bac91fdd73edcd0c671e9cf9c637192.json
│   │   │       ├── d86fcf85b6709df82c4acb9b6fa74661ab2a981974f328b773e3340d1af90132.json
│   │   │       ├── da2f24f1020f3a3347f25e4df62ba1456c345c36d6c52d8faf3ecb07a72cd909.json
│   │   │       ├── da891727e31df3879a11554f130bd7191bcb383c9b62ad5081fe2d7bfa3b7469.json
│   │   │       ├── db938628dc9dca89c5053072ceea91e7e582a07d199b4451ae2825644611b11b.json
│   │   │       ├── dc17ff36832acfc395a73561ee8b9a280d315dcffa97b7ab0fc69086f8199417.json
│   │   │       ├── dd7eeecc8b878559c59628d53494a1007ba70b83c02c05f143d99d484d577450.json
│   │   │       ├── df98e8cf721a39384a51626dabfd284a0e23aecd021ecf45170809b513a01bf9.json
│   │   │       ├── e27d10812018e61cd6627912716cf41003c8de80744ee1e27d0995e2d9f59db7.json
│   │   │       ├── e40602e113b0bcdb8025a4fda787e1afb5254e875e0d71a676fbeaab798af1c7.json
│   │   │       ├── e4ddedad5f09e9ff018b6e9cc09e9430296b220c4be536e7eb58c249150162ab.json
│   │   │       ├── e4f09a788b98fb93f4112f091f3c81e89c4177fc8a5eef7e86da2a22c0843d69.json
│   │   │       ├── e50736045a220835e3c00e55335bb9b1b6ec5086900859073c1c317da7ced039.json
│   │   │       ├── e508491b0a0b785134f5f88e5cf29827153768f5048a35401c67d41fb60e8c07.json
│   │   │       ├── e5d8eabb464ec52c32f7670d7a5380ab752157d55bea320d25f879686216930d.json
│   │   │       ├── e75d68fca216d336ba15ec607d083b50aacba2aec47e007e866c6f7f5b94088a.json
│   │   │       ├── e798a6a43e382cde25fb02b9b95cecfa496b4ee5bfbacb2a49b0c20eb7f16a59.json
│   │   │       ├── ea45cd2babfdc70e5d5b2512bde9bdb5aa18ca1033d5fa8814236922638314c9.json
│   │   │       ├── eaa9a6c787622d14a28c36cf59768ed2329c77ed55f4834fd128e24d179d8be9.json
│   │   │       ├── eaca52ff12997aa974ab5b26c5420cb806c0e07449198b9fcdf0901878880093.json
│   │   │       ├── ec87d77a88d4e05d7d6403e99be06cad76024b44b33bb9c57bd0462c120ddfa3.json
│   │   │       ├── ef5cbe77cc46414385a0efc62868a1c07bb1df357f4de19ea1a13dc4e78b313f.json
│   │   │       ├── f177c220de28c908c443543b40b245aa3342f72fc36a9a1cce9c002374302441.json
│   │   │       ├── f1da34ab8916a1ea3d294badf638bd5a5bc3f26ac2f1886c040a3cd7ef67b75a.json
│   │   │       ├── f3dcd5c3f7d053487bc1710dc361f447b82acc2551906915fe04f19c5c361ea9.json
│   │   │       ├── f3fdce5064f2659cfa335d132c24b0a19738a98c1b5db366482dd72c2f06d7dc.json
│   │   │       ├── f45c1e3d1ed0e5bfbf539c55903b4fdc1a9b05498d538e133144287413f785ea.json
│   │   │       ├── f45cf3dadc14b40c32b0541d2cc13660a2582dd09a81f6d8e888f9e62fce3660.json
│   │   │       ├── f685338518169ff0aee1097c4364c5890a07a8210b85671764f91607f1d29b16.json
│   │   │       ├── f9000cbbe75a946cc83718f8c5f91574b35cdd67716dd99a06e63dc65f7b0b13.json
│   │   │       ├── fb9cc606c046a58df9d05f009ff1d4c9e4ec82dc9cad58fe67d14d0cdbd1595a.json
│   │   │       ├── fc8b3044399cd3aa48ba866b108f437b500beee3727e1c7b3262cd05ebeb164b.json
│   │   │       ├── fcdf606b2eda99dec4bac1fd1174ce400eb5977332dd9dda6e7d1407059b5df7.json
│   │   │       ├── fd3252c571117e1dbc207e1abd210a9873730313063e255a17fa3e8ee9596a49.json
│   │   │       ├── fd33b573e3147c5c185d24d8c7e54e30f3c8b54b9881d8346a95632f69e61372.json
│   │   │       ├── fe276e3a6de52543a56899f4c2ba372914941aa65dd02229be251fbbc4fb8e01.json
│   │   │       ├── fec0b950eaea4e1049f060a3e51bda2ab9a68dec1a147cf8142bbbcc3d2eef0f.json
│   │   │       ├── fec741cf9aea2e033af82ea75cb4f7e5629301014db7c6325380df643f32ae89.json
│   │   │       ├── ff7c08f084e1637347f057dda126a28aa8110257e4155245a43b0e56761df81d.json
│   │   │       ├── ffc0af71741978220139d79058e1d9a7d77f2be6c320317db3ce456fb6fc2110.json
│   │   │       └── ffcc854479dd9279a8d61c029f455d73f99041f06165a7776389f422d7077301.json
│   │   ├── semantic/
│   │   │   └── pd5fd89c46bb5/
│   │   ├── last_query_stamp
│   │   └── stat-index.json
│   ├── .graphify_incremental.json
│   ├── .graphify_labels.json
│   ├── .graphify_labels_auto.json
│   ├── .graphify_python
│   ├── .graphify_root
│   ├── .graphify_uncached.txt
│   ├── GRAPH_REPORT.md
│   ├── cost.json
│   ├── graph.html
│   ├── graph.json
│   └── manifest.json
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── actions/
│   │   │   ├── App/
│   │   │   │   ├── Http/
│   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   ├── ConstantsController.ts
│   │   │   │   │   │   ├── NotificationController.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   └── index.ts
│   │   │   │   ├── Modules/
│   │   │   │   │   ├── Account/
│   │   │   │   │   │   ├── Presentation/
│   │   │   │   │   │   │   ├── Http/
│   │   │   │   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   │   │   │   ├── AccountController.ts
│   │   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   ├── Category/
│   │   │   │   │   │   ├── Presentation/
│   │   │   │   │   │   │   ├── Http/
│   │   │   │   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   │   │   │   ├── CategoryController.ts
│   │   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   ├── Transaction/
│   │   │   │   │   │   ├── Presentation/
│   │   │   │   │   │   │   ├── Http/
│   │   │   │   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   │   │   │   ├── RecurringTransactionController.ts
│   │   │   │   │   │   │   │   │   ├── TransactionController.ts
│   │   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   ├── Transfer/
│   │   │   │   │   │   ├── Presentation/
│   │   │   │   │   │   │   ├── Http/
│   │   │   │   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   │   │   │   ├── TransferController.ts
│   │   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   ├── User/
│   │   │   │   │   │   ├── Presentation/
│   │   │   │   │   │   │   ├── Http/
│   │   │   │   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   │   │   │   ├── Settings/
│   │   │   │   │   │   │   │   │   │   ├── ProfileController.ts
│   │   │   │   │   │   │   │   │   │   ├── SecurityController.ts
│   │   │   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   └── index.ts
│   │   │   │   └── index.ts
│   │   │   ├── Illuminate/
│   │   │   │   ├── Broadcasting/
│   │   │   │   │   ├── BroadcastController.ts
│   │   │   │   │   └── index.ts
│   │   │   │   ├── Routing/
│   │   │   │   │   ├── RedirectController.ts
│   │   │   │   │   └── index.ts
│   │   │   │   └── index.ts
│   │   │   ├── Inertia/
│   │   │   │   ├── DevTools/
│   │   │   │   │   ├── Http/
│   │   │   │   │   │   ├── EntriesController.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   └── index.ts
│   │   │   │   ├── Controller.ts
│   │   │   │   └── index.ts
│   │   │   ├── Laravel/
│   │   │   │   ├── Fortify/
│   │   │   │   │   ├── Http/
│   │   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   │   ├── AuthenticatedSessionController.ts
│   │   │   │   │   │   │   ├── ConfirmablePasswordController.ts
│   │   │   │   │   │   │   ├── ConfirmedPasswordStatusController.ts
│   │   │   │   │   │   │   ├── ConfirmedTwoFactorAuthenticationController.ts
│   │   │   │   │   │   │   ├── EmailVerificationNotificationController.ts
│   │   │   │   │   │   │   ├── EmailVerificationPromptController.ts
│   │   │   │   │   │   │   ├── NewPasswordController.ts
│   │   │   │   │   │   │   ├── PasswordResetLinkController.ts
│   │   │   │   │   │   │   ├── RecoveryCodeController.ts
│   │   │   │   │   │   │   ├── RegisteredUserController.ts
│   │   │   │   │   │   │   ├── TwoFactorAuthenticatedSessionController.ts
│   │   │   │   │   │   │   ├── TwoFactorAuthenticationController.ts
│   │   │   │   │   │   │   ├── TwoFactorQrCodeController.ts
│   │   │   │   │   │   │   ├── TwoFactorSecretKeyController.ts
│   │   │   │   │   │   │   ├── VerifyEmailController.ts
│   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   └── index.ts
│   │   │   │   ├── Passkeys/
│   │   │   │   │   ├── Http/
│   │   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   │   ├── PasskeyConfirmationController.ts
│   │   │   │   │   │   │   ├── PasskeyLoginController.ts
│   │   │   │   │   │   │   ├── PasskeyRegistrationController.ts
│   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   └── index.ts
│   │   │   │   ├── Sanctum/
│   │   │   │   │   ├── Http/
│   │   │   │   │   │   ├── Controllers/
│   │   │   │   │   │   │   ├── CsrfCookieController.ts
│   │   │   │   │   │   │   └── index.ts
│   │   │   │   │   │   └── index.ts
│   │   │   │   │   └── index.ts
│   │   │   │   └── index.ts
│   │   │   └── Livewire/
│   │   │       ├── Features/
│   │   │       │   ├── SupportFileUploads/
│   │   │       │   │   ├── FilePreviewController.ts
│   │   │       │   │   ├── FileUploadController.ts
│   │   │       │   │   └── index.ts
│   │   │       │   └── index.ts
│   │   │       ├── Mechanisms/
│   │   │       │   ├── FrontendAssets/
│   │   │       │   │   ├── FrontendAssets.ts
│   │   │       │   │   └── index.ts
│   │   │       │   ├── HandleRequests/
│   │   │       │   │   ├── HandleRequests.ts
│   │   │       │   │   └── index.ts
│   │   │       │   └── index.ts
│   │   │       └── index.ts
│   │   ├── components/
│   │   │   ├── accounts/
│   │   │   │   └── AccountModal.tsx
│   │   │   ├── categories/
│   │   │   │   └── CategoryModal.tsx
│   │   │   ├── recurring-transactions/
│   │   │   │   └── RecurringTransactionForm.tsx
│   │   │   ├── settings/
│   │   │   │   └── UpdatePasswordModal.tsx
│   │   │   ├── transactions/
│   │   │   │   └── TransactionForm.tsx
│   │   │   ├── ui/
│   │   │   │   ├── alert.tsx
│   │   │   │   ├── avatar.tsx
│   │   │   │   ├── badge.tsx
│   │   │   │   ├── breadcrumb.tsx
│   │   │   │   ├── button.tsx
│   │   │   │   ├── card.tsx
│   │   │   │   ├── checkbox.tsx
│   │   │   │   ├── collapsible.tsx
│   │   │   │   ├── combobox.tsx
│   │   │   │   ├── command.tsx
│   │   │   │   ├── dialog.tsx
│   │   │   │   ├── dropdown-menu.tsx
│   │   │   │   ├── icon.tsx
│   │   │   │   ├── input-otp.tsx
│   │   │   │   ├── input.tsx
│   │   │   │   ├── label.tsx
│   │   │   │   ├── navigation-menu.tsx
│   │   │   │   ├── placeholder-pattern.tsx
│   │   │   │   ├── popover.tsx
│   │   │   │   ├── select.tsx
│   │   │   │   ├── separator.tsx
│   │   │   │   ├── sheet.tsx
│   │   │   │   ├── sidebar.tsx
│   │   │   │   ├── skeleton.tsx
│   │   │   │   ├── sonner.tsx
│   │   │   │   ├── spinner.tsx
│   │   │   │   ├── switch.tsx
│   │   │   │   ├── toggle-group.tsx
│   │   │   │   ├── toggle.tsx
│   │   │   │   └── tooltip.tsx
│   │   │   ├── AmountDisplay.tsx
│   │   │   ├── NotificationDropdown.tsx
│   │   │   ├── alert-error.tsx
│   │   │   ├── app-content.tsx
│   │   │   ├── app-header.tsx
│   │   │   ├── app-logo-icon.tsx
│   │   │   ├── app-logo.tsx
│   │   │   ├── app-shell.tsx
│   │   │   ├── app-sidebar-header.tsx
│   │   │   ├── app-sidebar.tsx
│   │   │   ├── appearance-tabs.tsx
│   │   │   ├── breadcrumbs.tsx
│   │   │   ├── delete-user.tsx
│   │   │   ├── heading.tsx
│   │   │   ├── input-error.tsx
│   │   │   ├── manage-passkeys.tsx
│   │   │   ├── manage-two-factor.tsx
│   │   │   ├── nav-footer.tsx
│   │   │   ├── nav-main.tsx
│   │   │   ├── nav-user.tsx
│   │   │   ├── passkey-item.tsx
│   │   │   ├── passkey-register.tsx
│   │   │   ├── passkey-verify.tsx
│   │   │   ├── password-input.tsx
│   │   │   ├── text-link.tsx
│   │   │   ├── two-factor-recovery-codes.tsx
│   │   │   ├── two-factor-setup-modal.tsx
│   │   │   ├── user-info.tsx
│   │   │   └── user-menu-content.tsx
│   │   ├── contexts/
│   │   │   └── PrivacyContext.tsx
│   │   ├── hooks/
│   │   │   ├── use-appearance.tsx
│   │   │   ├── use-clipboard.ts
│   │   │   ├── use-current-url.ts
│   │   │   ├── use-flash-toast.ts
│   │   │   ├── use-initials.tsx
│   │   │   ├── use-mobile-navigation.ts
│   │   │   ├── use-mobile.tsx
│   │   │   └── use-two-factor-auth.ts
│   │   ├── layouts/
│   │   │   ├── app/
│   │   │   │   ├── app-header-layout.tsx
│   │   │   │   └── app-sidebar-layout.tsx
│   │   │   ├── auth/
│   │   │   │   ├── auth-card-layout.tsx
│   │   │   │   ├── auth-simple-layout.tsx
│   │   │   │   └── auth-split-layout.tsx
│   │   │   ├── settings/
│   │   │   │   └── layout.tsx
│   │   │   ├── app-layout.tsx
│   │   │   └── auth-layout.tsx
│   │   ├── lib/
│   │   │   ├── axios.ts
│   │   │   └── utils.ts
│   │   ├── pages/
│   │   │   ├── accounts/
│   │   │   │   └── index.tsx
│   │   │   ├── auth/
│   │   │   │   ├── confirm-password.tsx
│   │   │   │   ├── forgot-password.tsx
│   │   │   │   ├── login.tsx
│   │   │   │   ├── register.tsx
│   │   │   │   ├── reset-password.tsx
│   │   │   │   ├── two-factor-challenge.tsx
│   │   │   │   └── verify-email.tsx
│   │   │   ├── categories/
│   │   │   │   └── index.tsx
│   │   │   ├── recurring-transactions/
│   │   │   │   └── index.tsx
│   │   │   ├── settings/
│   │   │   │   ├── appearance.tsx
│   │   │   │   ├── danger-zone.tsx
│   │   │   │   ├── notifications.tsx
│   │   │   │   ├── profile.tsx
│   │   │   │   └── security.tsx
│   │   │   ├── transactions/
│   │   │   │   ├── create.tsx
│   │   │   │   ├── edit.tsx
│   │   │   │   └── index.tsx
│   │   │   ├── dashboard.tsx
│   │   │   └── welcome.tsx
│   │   ├── routes/
│   │   │   ├── accounts/
│   │   │   │   └── index.ts
│   │   │   ├── api/
│   │   │   │   ├── accounts/
│   │   │   │   │   └── index.ts
│   │   │   │   ├── categories/
│   │   │   │   │   └── index.ts
│   │   │   │   ├── recurring-transactions/
│   │   │   │   │   └── index.ts
│   │   │   │   ├── transactions/
│   │   │   │   │   └── index.ts
│   │   │   │   ├── transfers/
│   │   │   │   │   └── index.ts
│   │   │   │   └── index.ts
│   │   │   ├── appearance/
│   │   │   │   └── index.ts
│   │   │   ├── categories/
│   │   │   │   └── index.ts
│   │   │   ├── constants/
│   │   │   │   └── index.ts
│   │   │   ├── danger-zone/
│   │   │   │   └── index.ts
│   │   │   ├── default-livewire/
│   │   │   │   └── index.ts
│   │   │   ├── livewire/
│   │   │   │   └── index.ts
│   │   │   ├── login/
│   │   │   │   └── index.ts
│   │   │   ├── notifications/
│   │   │   │   ├── api/
│   │   │   │   │   └── index.ts
│   │   │   │   └── index.ts
│   │   │   ├── passkey/
│   │   │   │   └── index.ts
│   │   │   ├── password/
│   │   │   │   ├── confirm/
│   │   │   │   │   └── index.ts
│   │   │   │   └── index.ts
│   │   │   ├── profile/
│   │   │   │   └── index.ts
│   │   │   ├── recurring-transactions/
│   │   │   │   └── index.ts
│   │   │   ├── register/
│   │   │   │   └── index.ts
│   │   │   ├── sanctum/
│   │   │   │   └── index.ts
│   │   │   ├── security/
│   │   │   │   └── index.ts
│   │   │   ├── transactions/
│   │   │   │   └── index.ts
│   │   │   ├── two-factor/
│   │   │   │   ├── login/
│   │   │   │   │   └── index.ts
│   │   │   │   └── index.ts
│   │   │   ├── user-password/
│   │   │   │   └── index.ts
│   │   │   ├── verification/
│   │   │   │   └── index.ts
│   │   │   ├── well-known/
│   │   │   │   └── index.ts
│   │   │   └── index.ts
│   │   ├── types/
│   │   │   ├── auth.ts
│   │   │   ├── finance.ts
│   │   │   ├── global.d.ts
│   │   │   ├── index.ts
│   │   │   ├── navigation.ts
│   │   │   ├── ui.ts
│   │   │   └── vite-env.d.ts
│   │   ├── wayfinder/
│   │   │   └── index.ts
│   │   └── app.tsx
│   └── views/
│       └── app.blade.php
├── routes/
│   ├── api.php
│   ├── channels.php
│   ├── console.php
│   ├── settings.php
│   └── web.php
├── tests/
│   ├── Arch/
│   │   └── ArchitectureTest.php
│   ├── Feature/
│   │   ├── Account/
│   │   │   ├── AccountBalanceServiceTest.php
│   │   │   ├── AccountBusinessRuleTest.php
│   │   │   └── AccountModelTest.php
│   │   ├── Audit/
│   │   │   └── AuditTrailTest.php
│   │   ├── Auth/
│   │   │   ├── AuthenticationTest.php
│   │   │   ├── EmailVerificationTest.php
│   │   │   ├── PasswordConfirmationTest.php
│   │   │   ├── PasswordResetTest.php
│   │   │   ├── RegistrationTest.php
│   │   │   ├── TwoFactorChallengeTest.php
│   │   │   └── VerificationNotificationTest.php
│   │   ├── Category/
│   │   │   ├── CategoryBusinessRuleTest.php
│   │   │   └── CategoryModelTest.php
│   │   ├── Endpoints/
│   │   │   ├── AccountEndpointTest.php
│   │   │   ├── CategoryEndpointTest.php
│   │   │   ├── TransactionEndpointTest.php
│   │   │   └── TransferEndpointTest.php
│   │   ├── Policies/
│   │   ├── Settings/
│   │   │   ├── ProfileUpdateTest.php
│   │   │   └── SecurityTest.php
│   │   ├── Transaction/
│   │   │   ├── ProcessRecurringTransactionsTest.php
│   │   │   ├── TransactionBusinessRuleTest.php
│   │   │   └── TransactionModelTest.php
│   │   ├── Transfer/
│   │   │   ├── TransferBusinessRuleTest.php
│   │   │   └── TransferModelTest.php
│   │   ├── ApiSetupTest.php
│   │   ├── DashboardTest.php
│   │   └── ExampleTest.php
│   ├── Unit/
│   │   └── ExampleTest.php
│   ├── Pest.php
│   └── TestCase.php
├── .editorconfig
├── .env
├── .env.example
├── .gitattributes
├── .gitignore
├── .gitlab-ci.yml
├── .npmrc
├── .prettierignore
├── .prettierrc
├── GIT_WORKFLOW.md
├── README.md
├── architecture.md
├── artisan
├── components.json
├── composer.json
├── composer.lock
├── debug_constants.php
├── eslint.config.js
├── financial-tracker-direction.md
├── generate_tree.py
├── output.log
├── package.json
├── phpstan-output.txt
├── phpstan.neon
├── phpunit.xml
├── pint.json
├── pnpm-lock.yaml
├── pnpm-workspace.yaml
├── project_structure.md
├── tsconfig.json
├── vite.config.ts
└── vite.log
```
