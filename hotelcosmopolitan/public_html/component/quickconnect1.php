<div class="module-wrapper">
    <div class="right-module">
        <div class="module-child row mod-2">
            <div class="module-text col-9">
                <a href="tel:+917926426001">
                    <P>Call Now</P>
                </a>
            </div>
            <div class="module-icon col-3">
                <a href="tel:+917926426001">
                    <i class="fa-duotone fa-phone"></i>
                </a>
            </div>
        </div>
        <div class="module-child row mod-middle">
            <div class="module-text col-9">
                <a href="https://wa.me/+919099914802" target="blank" rel="noopener noreferrer">
                    <p>Chat With Us</p>
                </a>
            </div>
            <div class="module-icon col-3">
                <a href="https://wa.me/+919099914802" target="blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
            </div>
        </div>
        <div class="module-child row mod-3">
            <div class="module-text col-9">
                <a href="https://maps.app.goo.gl/JDTGKEVzoMuZ19Xb9" target="blank" rel="noopener noreferrer">
                    <p>Get Direction</p>
                </a>
            </div>
            <div class="module-icon col-3">
                <a href="https://maps.app.goo.gl/JDTGKEVzoMuZ19Xb9" target="blank" rel="noopener noreferrer">
                    <i class="fa-duotone fa-diamond-turn-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>


<!-- Style -->
<style>
    .module-wrapper {
        position: relative;
        z-index: 99999;
    }

    .right-module {
        position: fixed;
        left: 0px;
        top: 50%;
        transform: translateY(-50%);

    }

    .right-module .module-child {
        cursor: pointer;
        transform: translateX(-71%);
        transition: all ease 0.5s;
        margin-bottom: 1px;
    }

    .module-child:hover {
        transform: translateX(0);
    }

    .module-icon {
        background-color: var(--red);
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .module-icon i {
        color: #fff;
        font-size: 18px;
        padding: 15px 10px;
    }

    .module-text {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: var(--orange);
    }

    .module-text a,
    .module-text p {
        color: #fff;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .mod-0 .module-icon {
        border-radius: 4px 0 0 0;
    }

    .mod-1 .module-icon {
        border-radius: 0 0 0 4px;
    }

    .mod-2 .module-icon {
        border-radius: 0 4px 0 0;
    }

    .mod-3 .module-icon {
        border-radius: 0 0 4px 0;
    }

    .mod-middle .module-icon {
        border-radius: 0 0 0 0;
    }
</style>