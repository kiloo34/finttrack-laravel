# Feature Planning and Implementation Workflow Rule

Whenever proposing or implementing a new feature or architectural change in this project:

## 1. Mandatory Sequence Diagram in Implementation Plans
- Every design proposal or implementation plan for a new feature MUST include a detailed **Mermaid Sequence Diagram**.
- The diagram must clearly show the complete end-to-end flow:
  - User interaction triggers.
  - Presentation / UI component state transitions.
  - API requests and response lifecycles.
  - Domain / Application / Database processing.
  - Post-processing side-effects and navigation.

## 2. Mandatory Unit and Feature Testing
- Every feature implementation MUST include corresponding automated tests:
  - **Backend Tests:** Write Pest / PHPUnit Feature & Unit tests covering controller endpoints, services, use cases, domain events, listeners, and error handling.
  - **Frontend Verification:** Ensure TypeScript types (`npm run types:check`), ESLint (`npm run lint:check`), and component testing where applicable.
- Tests must be executed and verified before marking a feature as completed.
