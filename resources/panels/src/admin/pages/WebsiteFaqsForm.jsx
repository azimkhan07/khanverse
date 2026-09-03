import WebsiteFormPage from '../components/WebsiteFormPage';

const CONFIG = {
    label: 'FAQ',
    listPath: '/website/faqs',
    fetchUrl: '/admin/website/faqs/{id}',
    createUrl: '/admin/website/faqs',
    itemKey: 'faq',
    heading: 'Manage frequently asked questions.',
    fields: [
        { name: 'question', label: 'Question', type: 'text', required: true },
        { name: 'answer', label: 'Answer', type: 'html', minHeight: 110, placeholder: 'Answer…' },
        { name: 'sort_order', label: 'Sort Order', type: 'number', default: 0 },
        { name: 'status', label: 'Active', type: 'toggle' },
    ],
};

function WebsiteFaqsForm() {
    return <WebsiteFormPage config={CONFIG} />;
}

export default WebsiteFaqsForm;