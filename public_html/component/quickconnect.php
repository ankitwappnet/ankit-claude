<div class="quick-connect">
  <div class="call">
    <a href="tel:+919099914802"><i class="fa-light fa-phone" title="Call us"></i></a>
  </div>
  <div class="whatsapp">
    <a href="https://wa.me/+919099914802" target="blank" title="Chat with us"><i class="fa-brands fa-whatsapp"></i></a>
  </div>
  <div class="location">
    <a href="https://maps.app.goo.gl/RvE2k881e8HNHLaU6" title="Get direction" target="blank" rel="noreffer noopener"><i class="fa-light fa-location-arrow"></i></a>
  </div>
</div>


<style>
  .quick-connect {
    position: fixed;
    bottom: 10%;
    left: 0;
    z-index: 999999;
  }

  .quick-connect a {
    color: #fff !important;
  }

  .call {
    background-color: var(--red);
    color: white;
    font-size: 20px;
    border-radius: 0 10px 10px 0;
    height: 50px;
    width: 50px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
  }

  .whatsapp a {
    color: #fff;
  }

  .whatsapp {
    background-color: var(--red);
    color: #fff;
    font-size: 23px;
    border-radius: 0 10px 10px 0;
    height: 50px;
    width: 50px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .location {
    background-color: var(--red);
    color: #fff;
    font-size: 23px;
    border-radius: 0 10px 10px 0;
    height: 50px;
    width: 50px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 10px;
  }

  .call:hover {
    animation: shakes 0.82s cubic-bezier(.36, .07, .19, .97) both infinite;
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    perspective: 1000px;
  }

  .whatsapp:hover {
    animation: shakes 0.82s cubic-bezier(.36, .07, .19, .97) both infinite;
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    perspective: 1000px;
  }

  .location:hover {
    animation: shakes 0.82s cubic-bezier(.36, .07, .19, .97) both infinite;
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    perspective: 1000px;
  }



  @keyframes shakes {

    10%,
    90% {
      transform: translate3d(-1px, 0, 0);
    }

    20%,
    80% {
      transform: translate3d(2px, 0, 0);
    }

    30%,
    50%,
    70% {
      transform: translate3d(-4px, 0, 0);
    }

    40%,
    60% {
      transform: translate3d(4px, 0, 0);
    }
  }

  @keyframes shake {
    0% {
      -webkit-transform: translate(2px, 1px) rotate(0deg);
    }

    10% {
      -webkit-transform: translate(-1px, -2px) rotate(-1deg);
    }

    20% {
      -webkit-transform: translate(-3px, 0px) rotate(1deg);
    }

    30% {
      -webkit-transform: translate(0px, 2px) rotate(0deg);
    }

    40% {
      -webkit-transform: translate(1px, -1px) rotate(1deg);
    }

    50% {
      -webkit-transform: translate(-1px, 2px) rotate(-1deg);
    }

    60% {
      -webkit-transform: translate(-3px, 1px) rotate(0deg);
    }

    70% {
      -webkit-transform: translate(2px, 1px) rotate(-1deg);
    }

    80% {
      -webkit-transform: translate(-1px, -1px) rotate(1deg);
    }

    90% {
      -webkit-transform: translate(2px, 2px) rotate(0deg);
    }

    100% {
      -webkit-transform: translate(1px, -2px) rotate(-1deg);
    }
  }

  .hidden-txt {
    display: none;
  }
</style>