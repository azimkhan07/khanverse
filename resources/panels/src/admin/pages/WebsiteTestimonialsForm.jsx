import WebsiteFormPage from '../components/WebsiteFormPage';

const RATINGS = [
    ['1', '1 Star'],
    ['2', '2 Star'],
    ['3', '3 Star'],
    ['4', '4 Star'],
    ['5', '5 Star'],
];

const CONFIG = {
    label: 'Testimonial',
    listPath: '/website/testimonials',
    fetchUrl: '/admin/website/testimonials/{id}',
    createUrl: '/admin/website/testimonials',
    itemKey: 'testimonial',
    heading: 'Manage customer testimonials shown on the website.',
    fields: [
        { name: 'name', label: 'Name', type: 'text', required: true },
        { name: 'rating', label: 'Rating', type: 'select', default: '5', options: RATINGS },
        { name: 'designation', label: 'Designation', type: 'text' },
        { name: 'company', label: 'Company', type: 'text' },
        { name: 'review', label: 'Review', type: 'html', minHeight: 110, placeholder: 'Customer review…' },
        { name: 'image', label: 'Profile Image', type: 'image', preview: 'image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'sort_order', label: 'Sort Order', type: 'number', default: 0 },
        { name: 'status', label: 'Active', type: 'toggle' },
    ],
};

function WebsiteTestimonialsForm() {
    return <WebsiteFormPage config={CONFIG} />;
}

export default WebsiteTestimonialsForm;