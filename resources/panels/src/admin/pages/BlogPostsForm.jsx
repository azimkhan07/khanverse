import WebsiteFormPage from '../components/WebsiteFormPage';

const CONFIG = {
    label: 'Blog Post',
    listPath: '/website/blog-posts',
    fetchUrl: '/admin/website/blog-posts/{id}',
    createUrl: '/admin/website/blog-posts',
    itemKey: 'blog_post',
    heading: 'Manage blog posts shown on the website.',
    fields: [
        { name: 'title', label: 'Title', type: 'text', required: true, full: true },
        { name: 'slug', label: 'Slug', type: 'slug' },
        { name: 'category', label: 'Category', type: 'text', placeholder: 'e.g. Web Development' },
        { name: 'author', label: 'Author', type: 'text' },
        { name: 'excerpt', label: 'Excerpt', type: 'textarea', rows: 3, full: true, placeholder: 'Short summary shown on the blog card…' },
        { name: 'content', label: 'Content', type: 'html', minHeight: 220, full: true, placeholder: 'Write the full blog post…' },
        { name: 'cover_image', label: 'Cover Image', type: 'image', preview: 'cover_image_url', full: true, note: 'Leave empty to keep the current image.' },
        { name: 'published_at', label: 'Published At', type: 'datetime', omitIfEmpty: true },
        { name: 'meta_title', label: 'Meta Title', type: 'text' },
        { name: 'meta_keywords', label: 'Meta Keywords', type: 'text' },
        { name: 'meta_description', label: 'Meta Description', type: 'textarea', rows: 3 },
        { name: 'status', label: 'Active', type: 'toggle' },
    ],
};

function BlogPostsForm() {
    return <WebsiteFormPage config={CONFIG} />;
}

export default BlogPostsForm;