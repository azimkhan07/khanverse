import WebsiteFormPage from '../components/WebsiteFormPage';

const CONFIG = {
    label: 'Page',
    listPath: '/website/pages',
    fetchUrl: '/admin/website/pages/{id}',
    createUrl: '/admin/website/pages',
    itemKey: 'page',
    heading: 'Manage static pages like About, Contact and Faq.',
    fields: [
        { name: 'title', label: 'Title', type: 'text', required: true },
        { name: 'slug', label: 'Slug (auto)', type: 'slug', note: 'Slug is auto-generated from the title.' },
        { name: 'description', label: 'Description', type: 'html', minHeight: 110, placeholder: 'Page description…' },
        { name: 'banner_image', label: 'Banner Image', type: 'image', preview: 'banner_image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'meta_title', label: 'Meta Title', type: 'text' },
        { name: 'meta_keywords', label: 'Meta Keywords', type: 'textarea', rows: 3 },
        { name: 'meta_description', label: 'Meta Description', type: 'textarea', rows: 4 },
        { name: 'status', label: 'Active', type: 'toggle' },
    ],
};

function WebsitePagesForm() {
    return <WebsiteFormPage config={CONFIG} />;
}

export default WebsitePagesForm;