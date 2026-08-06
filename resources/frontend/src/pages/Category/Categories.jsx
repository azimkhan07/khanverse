import "../../theme/css/categories.css";
import { categories } from "../../components/category/categoriesData";

function Categories() {

    return (

        <section className="categories">

            <div className="section-heading">

                <span>Browse Services</span>

                <h2>Explore Popular Categories</h2>

                <p>
                    Discover skilled professionals from every category.
                </p>

            </div>

            <div className="category-grid">

                {categories.map((item) => {

                    const Icon = item.icon;

                    return (

                        <div
                            className="category-card"
                            key={item.id}
                        >

                            <div className="category-icon">
                                <Icon size={38} />
                            </div>

                            <h3>{item.title}</h3>

                            <p>{item.services}</p>

                        </div>

                    );

                })}

            </div>

        </section>

    );

}

export default Categories;
