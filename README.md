# Kanban SaaS (Trello-Lite) API

[![Laravel](https://img.shields.io/badge/Laravel-v13.0+-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Sanctum](https://img.shields.io/badge/Sanctum-Auth-blue?style=flat-square)](https://laravel.com/docs/sanctum)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Managed-336791?style=flat-square&logo=postgresql)](https://www.postgresql.org/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker)](https://www.docker.com/)

A professional, multi-tenant Kanban board API built with Laravel. This project provides a robust foundation for building SaaS applications like Trello, featuring workspaces, projects, real-time task movement, and strict authorization policies.

- **Check the Frontend of this project**: [I want to see it](https://github.com/aeam88/kanban-saas-frontend)


## 🚀 Key Features

- **Multi-tenancy**: Strictly scoped data by Workspaces. Users can belong to multiple workspaces with different roles.
- **Project Management**: Organize work into projects and boards (kanban columns).
- **Task Management**: Drag & drop ready tasks with positions, assignments, and due dates.
- **Real-time Events**: Broadcasts `TaskMoved` events for seamless live updates in the frontend.
- **Robust Authorization**: Granular Policies ensure users only access data they are permitted to.
- **Sanctum Authentication**: Secure API and SPA authentication.
- **Dockerized Environment**: Ready to run with Docker Compose (PostgreSQL).

## 🛠️ Tech Stack

- **Backend**: Laravel 13
- **Authentication**: Laravel Sanctum
- **Database**: PostgreSQL 14
- **Real-time**: Laravel Reverb / Events
- **Development**: Docker Compose, Vite

## 🗄️ Database Schema

The architecture follows a hierarchical structure to support SaaS multi-tenancy:

- **Users**: Authentication and identity.
- **Workspaces**: The top-level tenant container.
- **WorkspaceUser (Pivot)**: Manages workspace membership and roles (`admin`, `member`).
- **Projects**: Scoped to a workspace.
- **Boards**: Kanban columns within a project (e.g., "To Do", "Doing", "Done").
- **Tasks**: Units of work with position-based sorting.
- **Comments**: Collaborative feedback on tasks.

## 🏁 Getting Started

### Prerequisites

- PHP 8.3+
- Composer
- Docker & Docker Compose
- Node.js & NPM

### Installation

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd kanban-saas
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Start the database**:
   ```bash
   docker compose up -d db
   ```

5. **Run migrations**:
   ```bash
   php artisan migrate
   ```

6. **Start the development server**:
   ```bash
   composer run dev
   ```

## 📡 API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/register` | Register a new user |
| `POST` | `/api/login` | Authenticate and get token |
| `GET`  | `/api/me` | Get current user info |
| `POST` | `/api/logout` | Revoke current token |

### Workspaces
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET`  | `/api/workspaces` | List all user workspaces |
| `POST` | `/api/workspaces` | Create a new workspace |
| `GET`  | `/api/workspaces/{id}` | Show workspace details |
| `DELETE`| `/api/workspaces/{id}` | Delete a workspace |
| `POST` | `/api/workspaces/{id}/invite` | Invite user by email |

### Projects
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET`  | `/api/workspaces/{id}/projects` | List projects in workspace |
| `POST` | `/api/projects` | Create a project |
| `GET`  | `/api/projects/{id}/board` | Get full board view (columns & tasks) |
| `PUT`  | `/api/projects/{id}` | Update project details |
| `DELETE`| `/api/projects/{id}` | Delete a project |

### Boards (Columns)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET`  | `/api/projects/{id}/boards` | List columns for a project |
| `POST` | `/api/boards` | Create a new column |
| `PUT`  | `/api/boards/reorder` | Reorder columns in project |
| `DELETE`| `/api/boards/{id}` | Delete a column |

### Tasks & Comments
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET`  | `/api/boards/{id}/tasks` | List tasks in a column |
| `POST` | `/api/tasks` | Create a task |
| `PUT`  | `/api/tasks/{id}` | Update task details |
| `PATCH`| `/api/tasks/{id}/move` | Move task (reorder/change board) |
| `DELETE`| `/api/tasks/{id}` | Delete a task |
| `POST` | `/api/tasks/{id}/comments` | Add a comment to a task |

## 💡 Core Logic

### Drag & Drop Reindexing
To ensure consistent ordering across columns, we use an integer-based `position` system. When a task is moved, the backend updates its `board_id` and `position`.

### Authorization Logic
Every request is filtered through **Laravel Policies**. For example, a user can only view a project if they are a member of the project's workspace:

```php
public function view(User $user, Project $project)
{
    return $project->workspace->users->contains($user);
}
```

## 📄 License

Proprietary Software. All rights reserved. 

Unauthorized copying, modification, distribution, or use of this software via any medium is strictly prohibited.
