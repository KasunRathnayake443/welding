# Welding Company Website

A Core PHP + MySQL website for a welding and metal fabrication business.

This project is built as a **business website + portfolio + product showcase**, not a full e-commerce platform.

It allows the business owner to:

- manage products
- manage category-based product properties
- manage custom extra product properties
- manage product images
- manage portfolio items
- manage portfolio images
- manage customer inquiries
- manage site-wide business details

The public website allows visitors to:

- browse products
- view product details
- browse completed work / portfolio
- contact the business
- send inquiries
- use WhatsApp/contact calls to action

---

## Tech Stack

- Core PHP
- MySQL
- HTML
- CSS
- JavaScript

---

## Project Type

This is a **single monolithic website** with:

- public frontend
- admin panel
- one database
- one hosting environment

This makes it easier to deploy on normal shared hosting / cPanel hosting.

---

## Main Features

### Admin Panel
- Admin login/logout
- Dashboard
- Categories management
- Category properties management
- Products management
- Product images management
- Portfolio management
- Portfolio images management
- Inquiries management
- Site settings management

### Frontend
- Home page
- About page
- Products listing
- Product details page
- Portfolio listing
- Portfolio item details page
- Contact page with inquiry form

---

## Database

This project uses the existing database:

`welding_site`

### Main Tables
- `admins`
- `categories`
- `category_properties`
- `products`
- `product_property_values`
- `product_extra_properties`
- `product_images`
- `portfolio_items`
- `portfolio_images`
- `inquiries`
- `site_settings`

---

## Folder Structure

```text
welding-site/
│
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   │
│   ├── categories/
│   ├── category-properties/
│   ├── products/
│   ├── product-images/
│   ├── portfolio/
│   ├── portfolio-images/
│   ├── inquiries/
│   ├── settings/
│   │
│   └── includes/
│       ├── auth.php
│       ├── guest.php
│       ├── header.php
│       ├── footer.php
│       ├── sidebar.php
│       └── topbar.php
│
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── functions.php
│   ├── frontend-header.php
│   └── frontend-footer.php
│
├── assets/
│   ├── css/
│   │   ├── admin/
│   │   └── frontend/
│   ├── js/
│   │   └── frontend/
│   └── images/
│
├── uploads/
│   ├── products/
│   └── portfolio/
│
├── index.php
├── about.php
├── products.php
├── product.php
├── portfolio.php
├── portfolio-item.php
├── contact.php
└── README.md