import WebsiteFormPage from '../components/WebsiteFormPage';

const CONFIG = {
    label: 'Banner',
    listPath: '/website/banners',
    fetchUrl: '/admin/website/banners/{id}',
    createUrl: '/admin/website/banners',
    itemKey: 'banner',
    heading: 'Manage website banners and their positions.',
    fields: [
        { name: 'title', label: 'Title', type: 'text', required: true },
        { name: 'position', label: 'Position', type: 'select', default: 'homepage', options: [
            ['homepage', 'Homepage'],
            ['homepage_middle', 'Homepage Middle'],
            ['homepage_bottom', 'Homepage Bottom'],
            ['category', 'Category'],
            ['service', 'Service'],
            ['promotion', 'Promotion'],
        ] },
        { name: 'link', label: 'Link', type: 'text', placeholder: 'https://…' },
        { name: 'image', label: 'Banner Image', type: 'image', preview: 'image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'status', label: 'Active', type: 'toggle' },
    ],
};

function WebsiteBannersForm() {
    return <WebsiteFormPage config={CONFIG} />;
}

export default WebsiteBannersForm;