function Badge({

    children,

    color = "primary"

}) {

    return (

        <span className={`badge badge-${color}`}>

            {children}

        </span>

    );

}

export default Badge;
