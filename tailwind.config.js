module.exports = {
  purge: [
    "./resources/views/**/*.blade.php",
    "./resources/views/*.blade.php",
    "./resources/css/**/*.css",
    "./public/js/*.js",
    "./public/css/*.css",
  ],
  theme: {
    extend: {
      backgroundImage: {
        "custom-radial":
          "radial-gradient(circle, rgba(0,0,0,0) 0%, rgba(0,0,0,0.64) 64%)",
      },
      fontSize: {
        xxl: "3.25rem",
      },
      colors: {
        primary: "#38d430",
        darkGray: "#1c1c1c",
        mediumGray: "#A4A4A4",
        lightBlack: "#3c3c3c",
        mediumRed: "#f54e3d",
        darkYellow: "#b89659",
        lightGray: "#D9D9D9",
        grayText: "#767676",

        Line_00: "#DADADA",
        Line_03: "#323232",
        Line_02: "#545454",
        BG_Overlay_01: "#2B2B2B",

        Icon03: "#BEBEBE",
        Icon05: "#595959",
        Icon06: "#191919",

        TitleHighlight: "#B51001",

        Gray_16: "#1A1A1A",
        Gray_15: "#272727",
        Gray_14: "#323232",
        Gray_12: "#545454",
        Gray_03: "#F7F7F7",
        Gray_04: "#ECECEC",
        Gray_05: "#ECECEC",
        Gray_07: "#CCCCCC",
      },
      borderColor: {
        Line_02: "#545454",
      },
      fontFamily: {
        inter: ["Inter", "sans-serif"], // Add Inter as a custom font
      },
      spacing: {
        2.5: "0.625rem", // 10px
        3.5: "0.875rem", // 14px
        7.5: "1.875rem", // 30px
        12.5: "3.125rem", // 50px
        21: "5.25rem", // 84px
        30: "7.5rem", //120px
        32.5: "8.125rem", //130px
        33: "8.25rem", // 132px
        50: "12.5rem", //200px
        62.5: "15.625rem", // 250px
        75: "18.75rem", // 300px
        77.5: "19.375rem", // 310px
        110: "27.5rem", // 440px
        242.5: "60.625rem", // 970px
      },
      animation: {
        skeleton: "skeleton 1.2s infinite",
      },
      keyframes: {
        skeleton: {
          "0%": {
            transform: "translateX(-100%)",
          },
          "100%": {
            transform: "translateX(100%)",
          },
        },
      },
      screens: {
        md2: "900px", // Thêm breakpoint tùy chỉnh 900px
      },
    },
    container: {
      center: true, // Ensures the container is centered by default
      padding: {
        lg: "3rem",
        xl: "5rem",
      },
    },
  },
  variants: {},
  plugins: [],
};
