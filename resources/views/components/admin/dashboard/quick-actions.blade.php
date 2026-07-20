<div class="dashboard-action-card">

    <div class="dashboard-action-header">

        <div>

            <h5>

                <i class="fas fa-bolt me-2 text-warning"></i>

                Quick Actions

            </h5>

            <small>

                Frequently used shortcuts

            </small>

        </div>

    </div>

    <div class="row g-3 mt-2">

        <div class="col-6">

            <a href="{{ route('admin.website.pages.create') }}" class="quick-action">

                <div class="quick-icon bg-primary">

                    <i class="fas fa-file-alt"></i>

                </div>

                <h6>Add Page</h6>

                <small>CMS Page</small>

            </a>

        </div>

        <div class="col-6">

            <a href="{{ route('admin.website.banners.create') }}" class="quick-action">

                <div class="quick-icon bg-success">

                    <i class="fas fa-images"></i>

                </div>

                <h6>Add Banner</h6>

                <small>Homepage</small>

            </a>

        </div>

        <div class="col-6">

            <a href="{{ route('admin.website.faq.create') }}" class="quick-action">

                <div class="quick-icon bg-warning">

                    <i class="fas fa-question-circle"></i>

                </div>

                <h6>Add FAQ</h6>

                <small>Help Center</small>

            </a>

        </div>

        <div class="col-6">

            <a href="{{ route('admin.website.testimonials.create') }}" class="quick-action">

                <div class="quick-icon bg-info">

                    <i class="fas fa-comments"></i>

                </div>

                <h6>Testimonial</h6>

                <small>Customer Review</small>

            </a>

        </div>

        <div class="col-6">

            <a href="{{ route('admin.services.create') }}" class="quick-action">

                <div class="quick-icon bg-danger">

                    <i class="fas fa-cogs"></i>

                </div>

                <h6>Service</h6>

                <small>New Service</small>

            </a>

        </div>

        <div class="col-6">

            <a href="{{ route('admin.categories.create') }}" class="quick-action">

                <div class="quick-icon bg-dark">

                    <i class="fas fa-layer-group"></i>

                </div>

                <h6>Category</h6>

                <small>Manage</small>

            </a>

        </div>

    </div>

</div>

<style>
    .dashboard-action-card {

        background: #fff;

        border-radius: 20px;

        padding: 25px;

        box-shadow: 0 12px 30px rgba(0, 0, 0, .08);

        transition: .35s;

    }

    .dashboard-action-card:hover {

        transform: translateY(-5px);

        box-shadow: 0 22px 45px rgba(0, 0, 0, .14);

    }

    .dashboard-action-header h5 {

        font-weight: 700;

        margin: 0;

    }

    .dashboard-action-header small {

        color: #8b8b8b;

    }

    .quick-action {

        display: block;

        text-decoration: none;

        text-align: center;

        background: #f8fafc;

        border-radius: 18px;

        padding: 22px 10px;

        transition: .35s;

        color: #222;

        position: relative;

        overflow: hidden;

        border: 1px solid #eef2f7;

    }

    .quick-action::before {

        content: "";

        position: absolute;

        inset: 0;

        background: linear-gradient(135deg, rgba(79, 70, 229, .08), rgba(6, 182, 212, .08));

        opacity: 0;

        transition: .35s;

    }

    .quick-action:hover::before {

        opacity: 1;

    }

    .quick-action:hover {

        transform: translateY(-8px);

        color: #111;

        box-shadow: 0 15px 30px rgba(0, 0, 0, .12);

    }

    .quick-icon {

        width: 60px;

        height: 60px;

        margin: auto;

        border-radius: 18px;

        display: flex;

        justify-content: center;

        align-items: center;

        color: #fff;

        font-size: 24px;

        transition: .35s;

    }

    .quick-action:hover .quick-icon {

        transform: rotate(10deg) scale(1.12);

    }

    .quick-action h6 {

        margin-top: 16px;

        margin-bottom: 4px;

        font-weight: 700;

    }

    .quick-action small {

        color: #777;

    }
</style>
