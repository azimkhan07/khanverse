import UsersPage from '../components/UsersPage';

export default function Buyers() {
    return (
        <UsersPage
            title="Buyers"
            subtitle="All registered buyers"
            endpoint="/admin/users/buyers"
            detailPath="/buyers"
        />
    );
}
