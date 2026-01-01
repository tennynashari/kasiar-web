# API Documentation - Unified POS

Base URL: `http://localhost:8000/api`

## Authentication

All endpoints (except login) require Bearer token authentication.

### Login
```
POST /login
Content-Type: application/json

Body:
{
  "email": "kasir@kasir.app",
  "password": "password"
}

Response:
{
  "user": {
    "id": 1,
    "name": "Kasir Retail",
    "email": "kasir@kasir.app",
    "role": "kasir",
    "outlet_id": 1,
    "outlet": {...}
  },
  "token": "1|abc123..."
}
```

### Logout
```
POST /logout
Authorization: Bearer {token}

Response:
{
  "message": "Logged out successfully"
}
```

### Get Current User
```
GET /me
Authorization: Bearer {token}

Response:
{
  "id": 1,
  "name": "Kasir Retail",
  "email": "kasir@kasir.app",
  "role": "kasir",
  "outlet_id": 1,
  "outlet": {...}
}
```

## Products

### List Products
```
GET /products?search={query}&category_id={id}&per_page=50
Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "sku": "SKU-000001",
      "barcode": "2000000000016",
      "name": "Nasi Goreng",
      "category_id": 1,
      "category": {
        "id": 1,
        "name": "Makanan"
      },
      "cost_price": "15000.00",
      "selling_price": "25000.00",
      "stock": 0,
      "min_stock": 10,
      "track_stock": false,
      "is_active": true
    }
  ],
  "meta": {...},
  "links": {...}
}
```

### Create Product
```
POST /products
Authorization: Bearer {token}
Content-Type: application/json

Body:
{
  "name": "Product Name",
  "category_id": 1,
  "sku": "SKU-001", // optional, auto-generated
  "barcode": "1234567890123", // optional
  "cost_price": 10000,
  "selling_price": 15000,
  "stock": 100,
  "min_stock": 10,
  "track_stock": true,
  "description": "Product description"
}

Response: {product object}
```

### Update Product
```
PUT /products/{id}
Authorization: Bearer {token}
Content-Type: application/json

Body: {same as create}

Response: {product object}
```

### Delete Product
```
DELETE /products/{id}
Authorization: Bearer {token}

Response:
{
  "message": "Product deleted successfully"
}
```

### Find by Barcode
```
POST /products/find-barcode
Authorization: Bearer {token}
Content-Type: application/json

Body:
{
  "barcode": "2000000000016"
}

Response: {product object}
```

### Generate Barcode
```
POST /products/{id}/generate-barcode
Authorization: Bearer {token}

Response:
{
  "barcode": "2000000000016",
  "product": {...}
}
```

## Categories

### List Categories
```
GET /categories
Authorization: Bearer {token}

Response: [
  {
    "id": 1,
    "name": "Makanan",
    "slug": "makanan",
    "color": "#EF4444",
    "products_count": 5
  }
]
```

### Create Category
```
POST /categories
Authorization: Bearer {token}
Content-Type: application/json

Body:
{
  "name": "Category Name",
  "description": "Description",
  "color": "#3B82F6"
}

Response: {category object}
```

## Transactions

### List Transactions
```
GET /transactions?outlet_id={id}&date_from={date}&date_to={date}&status={status}
Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "transaction_no": "TRX202412310010001",
      "outlet_id": 1,
      "user_id": 3,
      "subtotal": "25000.00",
      "discount": "0.00",
      "tax": "0.00",
      "total": "25000.00",
      "paid_amount": "30000.00",
      "change_amount": "5000.00",
      "payment_method": "cash",
      "status": "completed",
      "created_at": "2024-12-31 10:30:00",
      "user": {...},
      "outlet": {...},
      "items": [...]
    }
  ]
}
```

### Create Transaction
```
POST /transactions
Authorization: Bearer {token}
Content-Type: application/json

Body:
{
  "outlet_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 25000,
      "discount": 0,
      "notes": "Extra pedas"
    }
  ],
  "discount": 0,
  "tax": 0,
  "payment_method": "cash",
  "paid_amount": 50000,
  "notes": "Optional notes",
  "table_id": null // For F&B
}

Response: {transaction object with items}
```

### Get Transaction Detail
```
GET /transactions/{id}
Authorization: Bearer {token}

Response: {transaction object with items, outlet, user}
```

### Void Transaction
```
POST /transactions/{id}/void
Authorization: Bearer {token}

Response:
{
  "message": "Transaction voided successfully"
}
```

## Dashboard

### Get Dashboard Stats
```
GET /dashboard?outlet_id={id}&date_from={date}&date_to={date}
Authorization: Bearer {token}

Response:
{
  "today": {
    "total_revenue": 1000000,
    "total_transactions": 50,
    "average_transaction": 20000,
    "cash_in_hand": 500000
  },
  "sales_chart": [
    {
      "date": "2024-12-25",
      "total": 500000
    }
  ],
  "top_products": [
    {
      "id": 1,
      "name": "Nasi Goreng",
      "total_quantity": 30,
      "total_revenue": 750000
    }
  ],
  "payment_breakdown": [
    {
      "payment_method": "cash",
      "count": 30,
      "total": 600000
    }
  ],
  "low_stock_products": [...]
}
```

### Sales Report
```
GET /reports/sales?outlet_id={id}&date_from={date}&date_to={date}&group_by={day|week|month}
Authorization: Bearer {token}

Response: [
  {
    "period": "2024-12-31",
    "transaction_count": 50,
    "total_revenue": 1000000,
    "total_discount": 50000,
    "average_transaction": 20000
  }
]
```

## Error Responses

### 400 Bad Request
```json
{
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found
```json
{
  "message": "Product not found"
}
```

### 500 Internal Server Error
```json
{
  "message": "Server error",
  "error": "..."
}
```

## Rate Limiting
- Login: 5 requests per minute
- Other endpoints: 60 requests per minute

## Pagination
List endpoints support pagination:
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

Response includes:
- `data` - Array of items
- `meta.current_page` - Current page
- `meta.total` - Total items
- `links.next` - Next page URL
- `links.prev` - Previous page URL
