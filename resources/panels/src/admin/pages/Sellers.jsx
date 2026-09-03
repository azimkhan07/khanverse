import UsersPage from '../components/UsersPage';

export default function Sellers() {
    return (
        <UsersPage
            title="Sellers"
            subtitle="All registered sellers"
            endpoint="/admin/users/sellers"
            detailPath="/sellers"
        />
    );
}
