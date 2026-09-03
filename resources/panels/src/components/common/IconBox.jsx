function IconBox({

    icon: Icon,

    title,

    description

}) {

    return (

        <div className="icon-box">

            <div className="icon-box-icon">

                <Icon size={28} />

            </div>

            <h3>

                {title}

            </h3>

            <p>

                {description}

            </p>

        </div>

    );

}

export default IconBox;
