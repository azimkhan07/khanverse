import WebsiteFormPage from '../components/WebsiteFormPage';

const CONFIG = {
    label: 'Maintenance',
    singular: true,
    listPath: '/website/maintenance',
    fetchUrl: '/admin/website/maintenance',
    heading: 'Control the website maintenance page.',
    fields: [
        { name: 'title', label: 'Title', type: 'text', required: true, grid: 2 },
        { name: 'message', label: 'Message', type: 'html', minHeight: 110, placeholder: 'Maintenance message…' },
        { name: 'image', label: 'Maintenance Image', type: 'image', preview: 'image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'button_text', label: 'Button Text', type: 'text' },
        { name: 'button_url', label: 'Button URL', type: 'text', placeholder: 'https://…' },
        { name: 'start_at', label: 'Start Time', type: 'datetime' },
        { name: 'end_at', label: 'End Time', type: 'datetime' },
        { name: 'status', label: 'Enable Maintenance Mode', type: 'toggle' },
    ],
};

function WebsiteMaintenance() {
    return <WebsiteFormPage config={CONFIG} />;
}

export default WebsiteMaintenance;