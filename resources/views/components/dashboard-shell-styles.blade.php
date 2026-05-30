<style>
    :root {
        --color-primary: #1F2937;
        --color-primary-dark: #111827;
        --color-primary-light: #F9FAFB;
        --color-accent: #F8B803;
        --color-white: #FFFFFF;
        --color-gray-light: #F3F4F6;
        --color-gray: #E5E7EB;
        --color-text: #111827;
        --color-text-light: #6B7280;
        --sidebar-width: 280px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--color-gray-light);
        color: var(--color-text);
        overflow-x: hidden;
    }

    .dashboard-container {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(180deg, #1F2937 0%, #111827 100%);
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
    }

    .sidebar-header {
        min-height: 72px;
        padding: 0 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }

    .logo-circle {
        width: 36px;
        height: 36px;
        background: var(--color-white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        flex: 0 0 36px;
        overflow: hidden;
    }

    .logo-circle img {
        width: 24px;
        height: 24px;
        object-fit: contain;
    }

    .logo-text {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-white);
        letter-spacing: 0;
        line-height: 1;
    }

    .sidebar-nav {
        flex: 1;
        padding: 1rem 0;
        overflow-y: auto;
        scrollbar-width: none;
    }

    .sidebar-nav::-webkit-scrollbar {
        width: 0;
        height: 0;
    }

    .nav-item {
        margin: 0.25rem 0.85rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .nav-item a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0.85rem;
        color: #FFFFFF;
        text-decoration: none;
        font-size: 0.84rem;
        font-weight: 500;
        line-height: 1.25;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .nav-item.active {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
    }

    .nav-item.active a {
        background: transparent;
        color: #FFFFFF;
        font-weight: 600;
        border-left: 3px solid var(--color-accent);
    }

    .nav-item:not(.active):hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .nav-icon {
        width: 19px;
        height: 19px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #CBD5E1;
        flex: 0 0 19px;
    }

    .nav-icon i {
        width: 18px;
        height: 18px;
    }

    .nav-item.active .nav-icon {
        color: var(--color-accent);
    }

    .logout-btn {
        margin: 0.85rem;
        padding: 0.7rem 1rem;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        color: var(--color-white);
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .logout-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .logout-btn svg,
    .logout-btn i {
        width: 16px;
        height: 16px;
    }

    .main-content {
        flex: 1;
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .header-bar {
        background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
        min-height: 72px;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #FFFFFF;
        letter-spacing: 0;
    }

    .content-area {
        flex: 1;
        padding: 2rem;
    }

    @media (max-width: 900px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.28s ease;
        }

        body.admin-sidebar-open .sidebar {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .header-bar {
            padding: 1rem 1.25rem;
        }

        .header-title {
            font-size: 1rem;
        }

        .content-area {
            padding: 1.25rem;
        }
    }
</style>
