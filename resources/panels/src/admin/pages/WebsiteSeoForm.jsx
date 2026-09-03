import WebsiteFormPage from '../components/WebsiteFormPage';

const RENDER_FOR = [
    ['homepage', 'Homepage'],
    ['about', 'About Us'],
    ['contact', 'Contact Us'],
    ['faq', 'FAQ'],
    ['services', 'Services'],
    ['categories', 'Categories'],
];

const ROBOTS = ['index,follow', 'noindex,nofollow', 'index,nofollow', 'noindex,follow'];

const CONFIG = {
    label: 'SEO Setting',
    listPath: '/website/seo',
    fetchUrl: '/admin/website/seo/{id}',
    createUrl: '/admin/website/seo',
    itemKey: 'seo',
    heading: 'Manage SEO metadata for website pages.',
    fields: [
        { name: 'page_key', label: 'Page', type: 'select', options: RENDER_FOR, required: true },
        { name: 'robots', label: 'Robots', type: 'select', default: 'index,follow', options: ROBOTS },
        { name: 'meta_title', label: 'Meta Title', type: 'text', grid: 2 },
        { name: 'meta_keywords', label: 'Meta Keywords', type: 'textarea', rows: 3 },
        { name: 'meta_description', label: 'Meta Description', type: 'textarea', rows: 4 },
        { name: 'canonical_url', label: 'Canonical URL', type: 'text', placeholder: 'https://…' },
        { name: 'og_title', label: 'OG Title', type: 'text' },
        { name: 'og_description', label: 'OG Description', type: 'textarea', rows: 4 },
        { name: 'og_image', label: 'OG Image', type: 'image', preview: 'og_image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'twitter_title', label: 'Twitter Title', type: 'text' },
        { name: 'twitter_description', label: 'Twitter Description', type: 'textarea', rows: 4 },
        { name: 'twitter_image', label: 'Twitter Image', type: 'image', preview: 'twitter_image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'status', label: 'Active', type: 'toggle' },
    ],
};

function WebsiteSeoForm() {
    return <WebsiteFormPage config={CONFIG} />;
}

export default WebsiteSeoForm;