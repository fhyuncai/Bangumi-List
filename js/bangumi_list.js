let main_Container = document.getElementById("bangumi_list_content");
let nav_Container = document.getElementById("bangumi_list_nav");

let bangumiData = null;
let bangumiItemData = null;

let configData = {
    pageNum: 0,
    pageNow: 0,
    // 每页显示的最多Item数量
    singleItemNum: 0,
    // 每页显示的最多的NAV数量
    singleNavNum: 5
}

function parseBangumiData(args) {
    if (!main_Container || !nav_Container) {
        main_Container = document.getElementById("bangumi_list_content");
        nav_Container = document.getElementById("bangumi_list_nav");
    }
    if (args.messageType == "bangumi_list_data") {
        bangumiData = args;
        bangumiItemData = args.messageContent.content;

        // 设置主颜色
        if (document.body.style.getPropertyValue("--bangumi_list_color") != bangumiData.messageContent.mainColor) {
            document.body.style.setProperty('--bangumi_list_color', bangumiData.messageContent.mainColor);
        }

        // 错误处理
        if (args.messageCode != 200 || !Array.isArray(bangumiItemData)) {
            main_Container.innerHTML = null;
            nav_Container.innerHTML = null;
            switch (args.messageCode){
                case 202:
                    main_Container.innerText = "_(:3」」好像还没有填写 Bangumi ID 呢";
                    break;
                default:
                    main_Container.innerText = args.messageContent.content;
            }
            return;
        }

        if (!bangumiItemData || bangumiItemData === "") {
            // 提示没有追番信息
            main_Container.innerHTML = null;
            nav_Container.innerHTML = null;
            main_Container.innerText = "w(ﾟДﾟ)w 好像还没有追番信息？";
            return;
        }
        // 生成分页信息
        configData.singleItemNum = (bangumiData.messageContent.singleItemNum <= 0 ? 10 : bangumiData.messageContent.singleItemNum);
        configData.singleNavNum = (bangumiData.messageContent.singleNavNum <= 0 ? 3 : bangumiData.messageContent.singleNavNum);
        if (configData.singleNavNum % 2 == 0) {
            configData.singleNavNum++;
        }
        // 总分页数
        configData.pageNum = Math.ceil(bangumiItemData.length / configData.singleItemNum);
        // 当前分页
        configData.pageNow = 1;
        JumpPage(configData.pageNow);
    } else {
        // TODO 获取错误
        alert("获取追番信息错误!");
    }
}

function JumpPage(index) {
    if (configData.pageNum == 0) {
        main_Container.innerHTML = "w(ﾟДﾟ)w 追番数据消失了？！";
        return;
    }
    if (index < 1 || index > configData.pageNum) {
        main_Container.innerHTML = "(╬▔皿▔)╯ 此地禁止使用魔法！";
        return;
    }

    // 生成当前的页面
    refreshItems(index);
    // 刷新导航栏
    refreshNav(index);
}

function refreshItems(index) {
    if (index != configData.pageNow) return;

    main_Container.innerHTML = null;

    main_Container.classList.add("bangumi_list_autofill")
    // 根据当前页拿到所需的Item
    let itemOrigin = (index - 1) * configData.singleItemNum;

    for (let i = 0; i < configData.singleItemNum; i++) {
        if (itemOrigin >= bangumiItemData.length) return;

        let obj = bangumiItemData[itemOrigin];
        let itemDom = getItemDom(obj);
        if (itemDom) {
            main_Container.appendChild(itemDom);
        }
        itemOrigin++;
    }
}

function refreshNav(index) {
    if (index != configData.pageNow) return;

    nav_Container.innerHTML = "";
    if (configData.pageNum <= 1) return;

    let prevBtn = getBtnDom("上一页", "上一页", true, prevPage);
    nav_Container.appendChild(prevBtn);

    let centerNavIndex = Math.ceil(configData.singleNavNum / 2);

    if (configData.pageNow < centerNavIndex + 1) {
        for (let i = 1; i <= configData.pageNum; i++) {
            let newBtn = null;
            if (i < configData.pageNow) {
                newBtn = getBtnDom(i, i);
            } else if (i == configData.pageNow) {
                newBtn = getBtnDom(i, i, false);
                newBtn.className = "current";
            } else if (i > configData.pageNow && i <= configData.singleNavNum) {
                newBtn = getBtnDom(i, i);
            } else if (i > configData.singleNavNum && configData.singleNavNum < configData.pageNum) {
                newBtn = getBtnDom("…", "…", false);
                newBtn.className = "none";
            }
            if (newBtn) {
                nav_Container.appendChild(newBtn);
            }
            if (i > configData.singleNavNum) {
                break;
            }
        }
        if (configData.pageNum > configData.singleNavNum) {
            let lastButton = getBtnDom(configData.pageNum, configData.pageNum);
            nav_Container.appendChild(lastButton);
        }

    } else if (configData.pageNow < configData.pageNum - centerNavIndex) {
        let startBtn = getBtnDom(1, 1);
        nav_Container.appendChild(startBtn);

        for (let i = 1; i <= configData.pageNum; i++) {
            //console.log("ss" + i);
            let newBtn = null;
            if (i <= configData.pageNow - centerNavIndex) {
                if (i == 1) {
                    newBtn = getBtnDom("…", "…", false);
                    newBtn.className = "none";
                } else {
                    i = configData.pageNow - centerNavIndex;
                    continue;
                }
            } else if (i == configData.pageNow) {
                newBtn = getBtnDom(i, i, false);
                newBtn.className = "current";
            } else if (i < configData.pageNow || i < configData.pageNow + centerNavIndex) {
                newBtn = getBtnDom(i, i, true);
            } else if (i >= configData.pageNow + centerNavIndex && configData.singleNavNum < configData.pageNum) {
                newBtn = getBtnDom("…", "…", false);
                newBtn.className = "none";
            }
            if (newBtn) {
                nav_Container.appendChild(newBtn);
            }
            if (i >= configData.pageNow + centerNavIndex) {
                break;
            }
        }
        let lastButton = getBtnDom(configData.pageNum, configData.pageNum);
        nav_Container.appendChild(lastButton);

    } else if (configData.pageNow >= configData.pageNum - centerNavIndex) {
        if (configData.singleNavNum < configData.pageNum) {
            let startBtn = getBtnDom(1, 1);
            nav_Container.appendChild(startBtn);
        }
        for (let i = 1; i <= configData.pageNum; i++) {
            let newBtn = null;
            if (i <= configData.pageNum - configData.singleNavNum) {
                if (i == 1 && configData.singleNavNum < configData.pageNum) {
                    newBtn = getBtnDom("…", "…", false);
                    newBtn.className = "none";
                } else {
                    i = configData.pageNum - configData.singleNavNum;
                    continue;
                }
            } else if (i == configData.pageNow) {
                newBtn = getBtnDom(i, i, false);
                newBtn.className = "current";
            } else if (i < configData.pageNow || i <= configData.pageNow + centerNavIndex) {
                newBtn = getBtnDom(i, i, true);
            }
            if (newBtn) {
                nav_Container.appendChild(newBtn);
            }
        }
    }

    let nextBtn = getBtnDom("下一页", "下一页", true, nextPage);
    nav_Container.appendChild(nextBtn);
}



// 生成按钮
function getBtnDom(index, content, regEvent = true, callback) {
    let nav_Btn = document.createElement("li");
    nav_Btn.innerText = content;
    if (regEvent) {
        nav_Btn.addEventListener("click", (ev) => {
            callback ? callback() : thePage(index);
        });
    }
    return nav_Btn;
}
// 生成追番信息
function getItemDom(item) {
    if (!item) return;
    //console.log(item);

    let ItemDom = document.createElement("div");
    ItemDom.className = "BangumiItem";

    let ItemUrl = document.createElement("a");
    ItemUrl.className = "BangumiUrl";
    ItemUrl.href = item.url;
    ItemUrl.target = "_blank";

    let ItemImg = document.createElement("img");
    ItemImg.className = "BangumiImg";
    ItemImg.src = item.images.replace("http:", "");

    let ItemText = document.createElement("div");
    ItemText.className = "BangumiText";

    let ItemTitle = document.createElement("div");
    ItemTitle.className = "BangumiTitle";
    ItemTitle.innerHTML = item.name_cn == "" ? item.name : item.name_cn

    let ItemInfo = document.createElement("div");
    ItemInfo.className = "BangumiInfo";
    ItemInfo.innerHTML = item.name + "<br>"
        + "放送开始: " + item.date + "<br>";

    let ItemProgress = document.createElement("div");
    ItemProgress.className = "BangumiProgress";
    let pAllNum = item.eps;
    let pNowNum = item.ep_status;
    let pNumText = pNowNum + "/" + pAllNum;
    let pFGWidth = 100;
    if (pAllNum != 0 && pAllNum !== "未知") {
        pFGWidth = pNowNum / pAllNum * 100;
    }

    let pText = document.createElement("div");
    pText.className = "ProgressText";
    pText.innerText = pNumText;

    let pProgress = document.createElement("div");
    pProgress.className = "ProgressFG";
    pProgress.style.width = pFGWidth + "%";

    ItemProgress.appendChild(pText);
    ItemProgress.appendChild(pProgress);

    ItemUrl.appendChild(ItemImg);
    ItemUrl.appendChild(ItemText);
    ItemUrl.appendChild(ItemProgress);

    ItemText.appendChild(ItemTitle);
    ItemText.appendChild(ItemInfo);

    ItemDom.appendChild(ItemUrl);

    return ItemDom;
}
function thePage(index) {
    if (thePage < 1 || thePage > configData.pageNum) {
        alert("参数非法");
        return;
    }
    configData.pageNow = index;
    JumpPage(index);
}

function nextPage() {
    if (configData.pageNow >= configData.pageNum) {
        configData.pageNow = configData.pageNum;
        return;
    }
    configData.pageNow++;
    JumpPage(configData.pageNow);
}

function prevPage() {
    if (configData.pageNow <= 1) {
        configData.pageNow = 1;
        return;
    }
    configData.pageNow--;
    JumpPage(configData.pageNow);
}
