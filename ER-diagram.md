erDiagram
    USERS {
        int id PK
        varchar name
        varchar email
        varchar password
    }

    CATEGORIES {
        int id PK
        varchar name
    }

    PRODUCTS {
        int id PK
        varchar name
        int category_id FK
        int quantity
        date expiry_date
    }

    CATEGORIES ||--o{ PRODUCTS : contains