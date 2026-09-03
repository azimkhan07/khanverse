import WebsiteFormPage from '../components/WebsiteFormPage';

const SECTIONS = [
    ['hero', 'Hero'],
    ['why_choose_us', 'Why Choose Us'],
    ['featured_categories', 'Featured Categories'],
    ['featured_services', 'Featured Services'],
    ['featured_sellers', 'Featured Sellers'],
    ['latest_projects', 'Latest Projects'],
    ['testimonials', 'Testimonials'],
    ['faq', 'FAQ'],
    ['cta', 'Call To Action'],
    ['newsletter', 'Newsletter'],
];

const CONFIG = {
    label: 'Homepage Section',
    listPath: '/website/homepage',
    fetchUrl: '/admin/website/homepage/{id}',
    createUrl: '/admin/website/homepage',
    itemKey: 'section',
    heading: 'Manage homepage sections, their content and ordering.',
    fields: [
        { name: 'section_key', label: 'Section', type: 'select', options: SECTIONS, required: true },
        { name: 'title', label: 'Title', type: 'text', required: true },
        { name: 'subtitle', label: 'Subtitle', type: 'text' },
        { name: 'description', label: 'Description', type: 'html', minHeight: 110, placeholder: 'Section description…' },
        { name: 'button_text', label: 'Button Text', type: 'text' },
        { name: 'button_url', label: 'Button URL', type: 'text', placeholder: 'https://…' },
        { name: 'image', label: 'Image', type: 'image', preview: 'image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'background_image', label: 'Background Image', type: 'image', preview: 'background_image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'icon', label: 'Icon', type: 'text', placeholder: 'fas fa-star' },
        { name: 'sort_order', label: 'Sort Order', type: 'number', default: 0 },
        { name: 'status', label: 'Active', type: 'toggle' },
    ],
};

function WebsiteHomepageForm() {
    return <WebsiteFormPage config={CONFIG} />;
}

export default WebsiteHomepageForm;