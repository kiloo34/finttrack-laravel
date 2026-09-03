# System Architecture Documentation: Modular Monolith with Clean Architecture in Laravel

## 1. Executive Summary & Architectural Goals

Dokumen ini mendefinisikan standar arsitektur sistem berbasis **Modular Monolith** yang dipadukan dengan prinsip **Clean Architecture (Hexagonal / Ports & Adapters)** pada ekosistem **Laravel**.

### 1.1 Tujuan Utama
- **Maintainability & Decoupling:** Mengisolasi logika domain bisnis dari dependensi framework/infrastruktur (database, third-party API, HTTP request).
- **High Cohesion & Low Coupling:** Mengelompokkan kode berdasarkan batas domain (*Bounded Context*), bukan sekadar tipe file teknis.
- **Testability:** Memungkinkan unit testing murni pada Domain & Application layer tanpa mocking database atau overhead framework yang berat.
- **Evolutionary Path:** Memudahkan ekstraksi modul menjadi *Microservice* independen di masa depan tanpa perlu *rewrite* kode domain secara masif.

---

## 2. High-Level Architectural Patterns

Sistem menggabungkan dua pola arsitektur utama:
1. **Modular Monolith (Horizontal Partitioning):** Membagi sistem menjadi modul-modul independen berdasarkan domain bisnis (misal: `Identity`, `Catalog`, `Order`, `Billing`).
2. **Clean Architecture (Vertical Partitioning):** Membagi setiap modul ke dalam 4 lapisan konsentris dengan *Dependency Rule* yang ketat.

```
+-------------------------------------------------------------------------+
|                              MODULE (e.g., Order)                       |
|                                                                         |
|   +-----------------------------------------------------------------+   |
|   | 1. Presentation Layer (Inbound Adapters)                        |   |
|   |    - HTTP Controllers, Form Requests, API Resources             |   |
|   |    - Console Commands, Queue Workers / Consumers                |   |
|   +--------------------------------+--------------------------------+   |
|                                    | (Calls)                            |
|   +--------------------------------v--------------------------------+   |
|   | 2. Application Layer (Use Cases / Orchestration)                |   |
|   |    - Use Cases / Actions, DTOs, Application Services            |   |
|   +--------------------------------+--------------------------------+   |
|                                    | (Operates on)                      |
|   +--------------------------------v--------------------------------+   |
|   | 3. Domain Layer (Core Business Rules - Pure PHP)                |   |
|   |    - Entities, Value Objects, Domain Events                     |   |
|   |    - Ports / Interfaces (Repositories, External Contracts)      |   |
|   +--------------------------------^--------------------------------+   |
|                                    | (Implements Interface)             |
|   +--------------------------------+--------------------------------+   |
|   | 4. Infrastructure Layer (Outbound Adapters)                     |   |
|   |    - Eloquent Repositories, Database Models & Mappings          |   |
|   |    - Third-party Adapters (Payment Gateways, Notification APIs) |   |
|   +-----------------------------------------------------------------+   |
+-------------------------------------------------------------------------+
```

### 2.1 The Dependency Rule
- **Arah Ketergantungan:** Dependensi hanya boleh mengarah ke dalam (*inward*).
- `Presentation` bergantung pada `Application`.
- `Application` bergantung pada `Domain`.
- `Infrastructure` bergantung pada `Domain` (mengimplementasikan interface domain).
- **Domain Layer** adalah lapisan paling dalam dan **TIDAK BOLEH** memiliki dependensi ke layer lain atau framework eksternal.

---

## 3. Layer Breakdown & Responsibilities

### 3.1 Domain Layer (Pure PHP Core)
Lapisan paling sakral. Mengandung aturan bisnis inti (*enterprise business rules*).
- **Entities:** Objek domain dengan identitas unik (ID) dan state yang dimutasi melalui method bisnis, bukan public setter.
- **Value Objects:** Objek *immutable* tanpa identitas yang merepresentasikan konsep pengukuran/deskripsi (misal: `Money`, `EmailAddress`, `OrderStatus`).
- **Domain Events:** Event yang merepresentasikan fakta bisnis yang telah terjadi (misal: `OrderPlacedDomainEvent`).
- **Domain Exceptions:** Exception khusus domain untuk memvalidasi invariant bisnis (misal: `OrderCannotBeCancelledException`).
- **Repository / Service Interfaces (Ports):** Kontrak abstraksi penyimpanan atau service eksternal yang dibutuhkan domain.

*Aturan:* Bebas dari dependensi Laravel (tanpa Eloquent `Model`, tanpa Facade, tanpa HTTP request).

### 3.2 Application Layer (Use Cases)
Mengatur alur proses bisnis spesifik aplikasi (*application-specific business rules*).
- **Use Cases / Actions:** Kelas eksekutor tunggal (*Single Responsibility*) yang merepresentasikan interaksi pengguna atau proses sistem (misal: `CreateOrderUseCase`).
- **DTOs (Data Transfer Objects):** Objek pembawa data terstruktur antara Presentation dan Application layer.
- **Event Listeners / Subscribers:** Handler internal untuk merespons Domain Event.

*Aturan:* Mengorkestrasi domain entity dan repository interface. Tidak boleh mengekspos logic database langsung.

### 3.3 Infrastructure Layer (Outbound Adapters)
Implementasi teknis dan integrasi eksternal.
- **Persistence (Eloquent Models & Repositories):** Mengubah database rows menjadi Domain Entities (*Hydration*) dan menyimpan entity ke database (*Persistence*).
- **External Services:** Implementasi komunikasi ke Payment Gateway, Third-Party SMS/Email, Cache, dsb.
- **Database Migrations:** Schema migration per modul.

### 3.4 Presentation Layer (Inbound Adapters)
Gerbang masuk request ke sistem.
- **HTTP Controllers:** Menerima HTTP request, melakukan delegasi ke Form Request untuk validasi HTTP, mapping ke DTO, dan memanggil Use Case.
- **Form Requests:** Validasi input HTTP awal.
- **API Resources / Transformers:** Memformat output domain/DTO menjadi respons JSON.
- **Artisan Commands / Cron Jobs:** Pintu masuk via CLI.

---

## 4. Directory Structure

Struktur direktori berbasis `app/Modules/`:

```text
app/
├── Modules/
│   ├── Order/
│   │   ├── Domain/
│   │   │   ├── Entities/
│   │   │   │   ├── Order.php
│   │   │   │   └── OrderItem.php
│   │   │   ├── ValueObjects/
│   │   │   │   ├── OrderId.php
│   │   │   │   ├── Money.php
│   │   │   │   └── OrderStatus.php
│   │   │   ├── Events/
│   │   │   │   └── OrderCreated.php
│   │   │   ├── Exceptions/
│   │   │   │   └── InvalidOrderOperationException.php
│   │   │   └── Repositories/
│   │   │       └── OrderRepositoryInterface.php
│   │   │
│   │   ├── Application/
│   │   │   ├── UseCases/
│   │   │   │   ├── CreateOrderUseCase.php
│   │   │   │   └── CancelOrderUseCase.php
│   │   │   ├── DTOs/
│   │   │   │   ├── CreateOrderInput.php
│   │   │   │   └── OrderOutput.php
│   │   │   └── Contracts/
│   │   │       └── OrderPublicApiInterface.php  # Public contract untuk modul lain
│   │   │
│   │   ├── Infrastructure/
│   │   │   ├── Persistence/
│   │   │   │   ├── Eloquent/
│   │   │   │   │   ├── Models/
│   │   │   │   │   │   └── OrderModel.php
│   │   │   │   │   └── Mappers/
│   │   │   │   │       └── OrderMapper.php
│   │   │   │   └── Repositories/
│   │   │   │       └── EloquentOrderRepository.php
│   │   │   ├── External/
│   │   │   │   └── MidtransPaymentAdapter.php
│   │   │   └── Database/
│   │   │       └── Migrations/
│   │   │           └── 2026_01_01_000000_create_orders_table.php
│   │   │
│   │   ├── Presentation/
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/
│   │   │   │   │   └── CreateOrderController.php
│   │   │   │   ├── Requests/
│   │   │   │   │   └── CreateOrderHttpRequest.php
│   │   │   │   └── Resources/
│   │   │   │       └── OrderHttpResponse.php
│   │   │   └── CLI/
│   │   │       └── CleanupStaleOrdersCommand.php
│   │   │
│   │   └── Providers/
│   │       ├── OrderServiceProvider.php
│   │       └── RouteServiceProvider.php
│   │
│   ├── Customer/
│   └── Payment/
│
└── Shared/
    ├── Domain/
    │   ├── ValueObjects/
    │   └── DomainEventInterface.php
    └── Infrastructure/
        └── EventBus/
```

---

## 5. Module Communication & Boundary Rules

Untuk menjaga integritas **Modular Monolith**, komunikasi antarmodul diatur dengan aturan ketat:

### 5.1 Rules of Isolation
1. **Dilarang Direct DB Query:** Modul `Order` dilarang melakukan query SQL atau join langsung ke tabel milik modul `Customer`.
2. **Dilarang Cross-Domain Entity Dependency:** Domain layer suatu modul tidak boleh meng-import Domain Entity milik modul lain.
3. **Komunikasi Antarmodul hanya melalui 2 Jalur:**
   - **Sinkronus:** Memanggil *Public API Interface / Contract* yang disediakan modul target.
   - **Asinkronus / Dekopel:** Menerbitkan (*publish*) dan mendengarkan (*subscribe*) *Domain Events* via In-Memory Event Bus Laravel.

### 5.2 Contoh Komunikasi Sinkronus (Public Contract)
```php
namespace App\Modules\Customer\Application\Contracts;

interface CustomerPublicApiInterface
{
    public function getCustomerSummary(string $customerId): ?CustomerSummaryDTO;
}
```

### 5.3 Event-Driven Architecture for Side Effects
Seluruh efek samping lintas modul (*side-effects*) seperti mencatat Ledger, mengirim notifikasi, atau memperbarui Cache **WAJIB** dikelola melalui **Domain Events** dan **Event Listeners**.
1. **Domain Event Generation:** Entitas menggunakan trait `HasDomainEvents` untuk merekam event yang berisi *primitive payloads* (tanpa membawa entitas utuh / model Eloquent).
2. **Repository Dispatching:** Repositori mem-flush event dari entitas dan menyiarkannya via fungsi `event()` bawaan Laravel. Eksekusi ini **WAJIB dibungkus di dalam `DB::transaction()`**.
3. **Application Listeners:** Listener ditugaskan pada Application layer modul penerima untuk bereaksi (Sinkron untuk integritas data mutlak, Asinkron untuk beban kerja ringan).


---

## 6. Implementation Code Guidelines

### 6.1 Domain Entity (Pure PHP)
```php
namespace App\Modules\Order\Domain\Entities;

use App\Modules\Order\Domain\Events\OrderCreated;
use App\Modules\Order\Domain\ValueObjects\OrderId;
use App\Modules\Order\Domain\ValueObjects\Money;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Domain\Exceptions\InvalidOrderOperationException;
use App\Shared\Domain\Traits\HasDomainEvents;

final class Order
{
    use HasDomainEvents;

    private function __construct(
        private readonly OrderId $id,
        private readonly string $customerId,
        private Money $totalAmount,
        private OrderStatus $status,
        private readonly \DateTimeImmutable $createdAt
    ) {}

    public static function create(string $customerId, Money $totalAmount): self
    {
        $order = new self(
            id: OrderId::generate(),
            customerId: $customerId,
            totalAmount: $totalAmount,
            status: OrderStatus::PENDING,
            createdAt: new \DateTimeImmutable()
        );

        $order->recordEvent(new OrderCreated($order->getId()->value(), $customerId));

        return $order;
    }

    public function markAsPaid(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new InvalidOrderOperationException("Order cannot be marked as paid in current state: {$this->status->value}");
        }

        $this->status = OrderStatus::PAID;
    }

    // Getters
    public function getId(): OrderId { return $this->id; }
    public function getCustomerId(): string { return $this->customerId; }
    public function getTotalAmount(): Money { return $this->totalAmount; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
```

### 6.2 Port / Repository Interface
```php
namespace App\Modules\Order\Domain\Repositories;

use App\Modules\Order\Domain\Entities\Order;
use App\Modules\Order\Domain\ValueObjects\OrderId;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(OrderId $id): ?Order;
}
```

### 6.3 Use Case (Application Layer)
```php
namespace App\Modules\Order\Application\UseCases;

use App\Modules\Order\Application\DTOs\CreateOrderInput;
use App\Modules\Order\Application\DTOs\OrderOutput;
use App\Modules\Order\Domain\Entities\Order;
use App\Modules\Order\Domain\ValueObjects\Money;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;

final class CreateOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {}

    public function execute(CreateOrderInput $input): OrderOutput
    {
        $order = Order::create(
            customerId: $input->customerId,
            totalAmount: Money::fromDecimal($input->amount, $input->currency)
        );

        $this->orderRepository->save($order);

        return OrderOutput::fromDomain($order);
    }
}
```

### 6.4 Infrastructure Eloquent Adapter & Mapper
```php
namespace App\Modules\Order\Infrastructure\Persistence\Repositories;

use App\Modules\Order\Domain\Entities\Order;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderId;
use App\Modules\Order\Infrastructure\Persistence\Eloquent\Models\OrderModel;
use App\Modules\Order\Infrastructure\Persistence\Eloquent\Mappers\OrderMapper;

final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            OrderModel::query()->updateOrCreate(
                ['id' => $order->getId()->value()],
                [
                    'customer_id' => $order->getCustomerId(),
                    'total_amount' => $order->getTotalAmount()->getAmountInCents(),
                    'currency' => $order->getTotalAmount()->getCurrency(),
                    'status' => $order->getStatus()->value,
                    'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                ]
            );

            foreach ($order->flushEvents() as $event) {
                event($event);
            }
        });
    }

    public function findById(OrderId $id): ?Order
    {
        $model = OrderModel::query()->find($id->value());
        return $model ? OrderMapper::toDomain($model) : null;
    }
}
```

### 6.5 Presentation Controller
```php
namespace App\Modules\Order\Presentation\Http\Controllers;

use App\Modules\Order\Application\UseCases\CreateOrderUseCase;
use App\Modules\Order\Presentation\Http\Requests\CreateOrderHttpRequest;
use App\Modules\Order\Presentation\Http\Resources\OrderHttpResponse;
use Illuminate\Http\JsonResponse;

final class CreateOrderController
{
    public function __invoke(
        CreateOrderHttpRequest $request,
        CreateOrderUseCase $useCase
    ): JsonResponse {
        $input = $request->toDto();
        $output = $useCase->execute($input);

        return (new OrderHttpResponse($output))
            ->response()
            ->setStatusCode(201);
    }
}
```

---

## 7. Architecture Enforcement & Automated Testing

Untuk menjamin kepatuhan developer terhadap arsitektur tanpa degradasi seiring waktu, gunakan **Pest Architecture Testing** (`pestphp/pest-plugin-arch`):

```php
// tests/Arch/ArchitectureTest.php

test('domain layer does not depend on infrastructure, presentation, or laravel framework')
    ->expect('App\\Modules\\*\\Domain')
    ->toOnlyDependOn([
        'App\\Modules\\*\\Domain',
        'App\\Shared\\Domain',
        'DateTimeImmutable',
        'InvalidArgumentException',
        'Exception',
    ]);

test('application layer does not depend on presentation or infrastructure')
    ->expect('App\\Modules\\*\\Application')
    ->not->toUse([
        'App\\Modules\\*\\Presentation',
        'App\\Modules\\*\\Infrastructure',
        'Illuminate\\Http',
        'Illuminate\\Database',
    ]);

test('presentation layer only calls application layer')
    ->expect('App\\Modules\\*\\Presentation')
    ->not->toUse('App\\Modules\\*\\Infrastructure');

test('modules do not cross-import internal domain entities')
    ->expect('App\\Modules\\Order\\Domain')
    ->not->toUse('App\\Modules\\Customer\\Domain');
```

---

## 8. Summary Checklist for Code Reviews

- [ ] Apakah logic bisnis diletakkan di `Domain Entity` atau `Use Case` (bukan di Controller atau Eloquent Model)?
- [ ] Apakah `Domain` benar-benar terisolasi dari Laravel Framework / Eloquent?
- [ ] Apakah transfer data antar layer menggunakan DTO / Value Objects?
- [ ] Apakah interaksi database menggunakan abstraksi Repository Interface?
- [ ] Apakah migration dan model terisolasi per folder modul?
- [ ] Apakah Pest Architecture Test lulus di pipeline CI/CD?
