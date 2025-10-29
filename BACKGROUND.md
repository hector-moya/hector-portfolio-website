# Hector CMS Project Background

> **Note**: This document serves as a living guide to understand the current state of the project and what features are planned next. Please refer to this document for context on the project's direction and progress.

## Project Overview

Hector CMS is a modern, flexible Content Management System built with the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire) inspired by Statamic CMS. The project aims to provide a robust, developer-friendly CMS with a focus on content flexibility and user experience.

## Current Features

### Content Management
- **Blueprints**: Define content structures with flexible field types
- **Collections**: Organize content with custom routing (currently static)
- **Entries**: Dynamic content creation based on blueprints
- **Field Types**: Rich set of field types including complex ones like repeaters and selects

### Taxonomy System
- **Taxonomies**: Create custom classification systems (categories, tags, etc.)
- **Terms**: Manage taxonomy terms
- **Polymorphic Relations**: Associate taxonomies with various models

### User Management
- **Authentication**: Secure login with 2FA support
- **Authorization**: Role-based access control
- **Policies**: Fine-grained CRUD permissions

### Frontend
- **Entry Rendering**: Basic frontend view system
- **Preview System**: View entries before publishing

### Globals System
- **Global Sets**: Organize site-wide variables into logical groups
- **Blueprint Integration**: Use flexible field types for global variables
- **Caching**: Efficient caching with automatic invalidation
- **Facade Access**: Clean API through Laravel Facades
- **Policy Control**: Role-based access to global settings

### Navigation Builder System (✅ Complete)
- **Menu Management**:
  - CRUD operations for navigation menus
  - Handle-based access for efficient lookups
  - Hierarchical structure with nested items
  - Polymorphic relationships for dynamic linking
- **Admin Interface**:
  - Drag & Drop organization with Livewire + AlpineJS
  - Nested sorting with parent-child relationships 
  - Visual indication of menu structure
  - Item properties editor (URL, title, target, etc.)
- **Frontend Features**:
  - Blade components for menu rendering
  - Active state tracking based on current URL
  - Support for nested dropdowns
  - Flexible styling with Tailwind classes
  - Dark mode support out of the box
- **Developer Tools**:
  - Navigation facade for easy access
  - Service layer for business logic
  - Cache integration for performance
  - Full test coverage with Pest
  - Frontend and backend component tests

## Feature Implementation Status

| Feature                    | Status      | Progress | Notes                                          |
|---------------------------|-------------|----------|------------------------------------------------|
| Blueprints                | ✅ Complete  | 100%     | Includes complex field types                   |
| Collections               | ⚠️ Partial   | 80%      | Needs dynamic routing implementation           |
| Entries                   | ✅ Complete  | 100%     | Full CRUD with preview                         |
| Taxonomies                | ✅ Complete  | 100%     | With polymorphic relations                     |
| User Management           | ✅ Complete  | 100%     | Includes roles and policies                    |
| Frontend Rendering        | ⚠️ Partial   | 70%      | Basic implementation, needs enhancement        |
| Asset Management          | ✅ Complete  | 100%     | File upload, organization, S3 support         |
| Globals                  | ✅ Complete  | 100%     | Implemented with Facades, caching, and sets    |
| Navigation Builder       | ✅ Complete    | 100%   | Full menu management system with frontend rendering |
| Contact Forms            | 🔄 Planned  | 0%       | With SES integration                          |
| Publishing Workflow      | 🔄 Planned  | 0%       | Draft/published states + scheduling            |
| Revisions               | 🔄 Planned  | 0%       | Content version control                        |
| Relationships           | 🔄 Planned  | 0%       | Content relationships/references               |
| Fieldsets               | 🔄 Planned  | 0%       | Reusable field groups                         |
| Search & API            | 🔄 Planned  | 0%       | Search functionality and API access            |
| Localization            | 🔄 Planned  | 0%       | Multi-language support                        |

### Asset Management System
- **File Management**: Upload, move, delete, and organize files
- **Storage Options**: Local disk and S3 support
- **Organization**: Folder-based organization with search
- **Integration**: Image and File field types for content
- **Security**: Role-based access control with policies
- **Browser Component**: Reusable asset browser modal
- **Field Types**: Asset browser integration with form fields
- **Type Support**: Image, document, and general file support
- **Search**: Filename and metadata search capability
- **Upload Flow**: Visual upload progress with validation
   - Secure URL generation for assets

The focus is on completing the core asset management interface and field type integration first, followed by the more advanced features like CDN integration and image transformations.
