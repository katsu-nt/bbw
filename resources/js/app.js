// INSERT ADVERTISEMENT
const categorySizebarTop1Elements = document.getElementsByClassName(
  "ads-category-sizebar-top1"
);
if (categorySizebarTop1Elements.length > 0 && Category_Sizebar_Top1.data[0]) {
  const embedContent = Category_Sizebar_Top1.data[0]["embed"];

  // Iterate through all elements and set innerHTML
  for (let i = 0; i < categorySizebarTop1Elements.length; i++) {
    categorySizebarTop1Elements[i].innerHTML = embedContent;
  }
}

const homeSizebarBottomElements = document.getElementsByClassName(
  "ads-home-sizebar-bottom"
);
if (homeSizebarBottomElements.length > 0 && Home_Sizebar_Bottom.data[0]) {
  const embedContent = Home_Sizebar_Bottom.data[0]["embed"];

  // Iterate through all elements and set innerHTML
  for (let i = 0; i < homeSizebarBottomElements.length; i++) {
    homeSizebarBottomElements[i].innerHTML = embedContent;
  }
}

const homeCongngheChuyendeElements = document.getElementsByClassName(
  "ads-home-congnghe-chuyende"
);
if (homeCongngheChuyendeElements.length > 0 && home_congnghe_chuyende.data[0]) {
  const embedContent = home_congnghe_chuyende.data[0]["embed"];

  // Iterate through all elements and set innerHTML
  for (let i = 0; i < homeCongngheChuyendeElements.length; i++) {
    homeCongngheChuyendeElements[i].innerHTML = embedContent;
  }
}

const detailSizebarElements =
  document.getElementsByClassName("ads-detail-sizebar");
if (detailSizebarElements.length > 0 && Detail_Sizebar.data[0]) {
  const embedContent = Detail_Sizebar.data[0]["embed"];

  // Iterate through all elements and set innerHTML
  for (let i = 0; i < detailSizebarElements.length; i++) {
    detailSizebarElements[i].innerHTML = embedContent;
  }
}
