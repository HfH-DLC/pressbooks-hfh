document.addEventListener("DOMContentLoaded", () => {
  (async () => {
    const colorsElement = document.querySelector(".pressbooks-hfh-confetti");
    if (colorsElement === null) {
      return;
    }
    const colors = colorsElement.dataset.colors.split(",");
    await tsParticles.load("tsparticles", {
      fullScreen: {
        enable: true,
        zIndex: 1,
      },
      interactivity: {
        detectsOn: "window",
      },
      emitters: [
        {
          position: {
            x: 0,
            y: 100,
          },
          rate: {
            quantity: 100,
            delay: 1,
          },
          life: {
            count: 1,
            duration: 3,
          },
          particles: {
            move: {
              direction: "top-right",
            },
          },
        },
        {
          position: {
            x: 100,
            y: 100,
          },
          rate: {
            quantity: 100,
            delay: 1,
          },
          life: {
            count: 1,
            duration: 3,
          },
          particles: {
            move: {
              direction: "top-left",
            },
          },
        },
      ],
      particles: {
        color: {
          value: colors,
        },
        move: {
          decay: 0.05,
          enable: true,
          gravity: {
            enable: true,
          },
          outModes: {
            top: "none",
            default: "destroy",
          },
          speed: { min: 50, max: 150 },
        },
        rotate: {
          value: {
            min: 0,
            max: 360,
          },
          direction: "random",
          animation: {
            enable: true,
            speed: 30,
          },
        },
        tilt: {
          direction: "random",
          enable: true,
          value: {
            min: 0,
            max: 360,
          },
          animation: {
            enable: true,
            speed: 30,
          },
        },
        size: {
          value: 8,
        },
        roll: {
          darken: {
            enable: true,
            value: 10,
          },
          enable: true,
          speed: {
            min: 5,
            max: 15,
          },
        },
        wobble: {
          distance: 30,
          enable: true,
          speed: {
            min: -7,
            max: 7,
          },
        },
      },
    });
    console.log("tsParticles.load finished");
  })();
});
