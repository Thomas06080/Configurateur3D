<!DOCTYPE html>
<html lang="en">
<head>
    <title>Configurateur</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link type="text/css" rel="stylesheet" href="main.css">
</head>

<body>
<div class="parent">

    <div id="info">
        <a id="checkbox"><input id="checkboxInput" type="checkbox" name=checkbox checked> Affichage</a>
        <a><img id="menu-logo" src="miniatures/hamburger.png"></a>
        <a><img id="info-image" src="miniatures/info.png"></a>
        <a style="display: none" id="info-txt"></a>
        <div id="inter" class="inter" style="width :22rem;">

            <span>Choisir un Produit:
                <select class="select-css" id="body-models">
                    <option id="e-liquide">E-Liquide</option>
                    <option id="carte">Carte de visite</option>
                </select>
            </span>

            <br>

            <span>Choisissez parmis les nombreux choix de design: <select class="select-css" id="body-mat"></select></span>

            <br>

            <button id="button-usdz">Fichiers Usdz</button>

        </div>
            <div class="grille" id="grille"></div>

        <a id="auto-rotate"><img id="img360" src="miniatures/rotate.png"></a>
    </div>
</div>

<div id="container"></div>

<script type="module">

    import * as THREE from '../build/three.module.js';
    import Stats from './jsm/libs/stats.module.js';
    import {GLTFLoader} from './jsm/loaders/GLTFLoader.js';
    import {DRACOLoader} from './jsm/loaders/DRACOLoader.js';
    import {OrbitControls} from './jsm/controls/OrbitControls.js';
    import {PMREMGenerator} from './jsm/pmrem/PMREMGenerator.js';
    import {PMREMCubeUVPacker} from './jsm/pmrem/PMREMCubeUVPacker.js';
    import {EquirectangularToCubeGenerator} from './jsm/loaders/EquirectangularToCubeGenerator.js';
    import {RGBELoader} from './jsm/loaders/RGBELoader.js';
    ////INITIALISATION DES VARIABLES////
    let scnCamera, scene, meshFloor, renderer, objModel, materialsLib, envMap, controls;

    let bodyMatSelect = document.getElementById('body-mat');
    let bodyModelSelect = document.getElementById('body-models');
    let grilleSelect = document.getElementById('grille');
    let rotate = document.getElementById('auto-rotate');
    let info = document.getElementById('info-image');
    let infoTxt = document.getElementById('info-txt');
    let buttonUsdz = document.getElementById('button-usdz');
    let menuLogo = document.getElementById('menu-logo');
    let inter = document.getElementById('inter');
    let checkboxAffichage = document.querySelector("input[name=checkbox]");

    let liens;
    let n =0;
    let i = 0;
    let t=0;
    let x=0;

    let modelParts = {
        body: [],
    };

    let scnCameraTarget = new THREE.Vector3();
    ////INITIALISATION DES VARIABLES FIN////

    ////DEBUT addEventListener() List////
    bodyModelSelect.addEventListener('change', updateModel);
    bodyMatSelect.addEventListener('change', updateMaterials);
    bodyModelSelect.addEventListener('change', initMaterialSelectionMenus);
    rotate.addEventListener("click",OnOffRotation);
    info.addEventListener('click',OnOffInfo)
    buttonUsdz.addEventListener('click',OnOffUsdz)
    menuLogo.addEventListener('click',OnOffMenu)
    checkboxAffichage.addEventListener('change',checkboxAfficher)
    ////FIN addEventListener() List////

    ////INITIALISATION////
    function init() {

        let container = document.getElementById('container');

        scene = new THREE.Scene();

        scnCamera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.001, 100);
        scnCamera.position.set(30, 5, 0);

        let urls = ['px.jpg', 'nx.jpg', 'py.jpg', 'ny.jpg', 'pz.jpg', 'nz.jpg'];
        let loader = new THREE.CubeTextureLoader().setPath('textures/cube/studio/');

        loader.load(urls, function (texture) {

            let helper = new THREE.CameraHelper(scnCamera);
            scene.add(helper);

            ////ORBIT CONTROLS////
            let controls = new OrbitControls(scnCamera, renderer.domElement);

            controls.enablePan = false;
            controls.enableZoom = true;
            controls.minDistance = 1;
            controls.maxDistance = 3.5;
            controls.enableDamping = true;
            controls.dampingFactor = 0.2;
            controls.minPolarAngle = 0.1; //Uper
            controls.maxPolarAngle = 1.8; //Lowers
            controls.target.set(0, 0.6, 0);
            controls.autoRotate = true;
            controls.autoRotateSpeed = 0.05;
            controls.update();

            ////ORBIT CONTROLS FIN////

            ////FOND IMAGE DE LA SCENE////
            scene = new THREE.Scene();
            scene.background = new THREE.TextureLoader().load("textures/gris.jpg"); // (Ciel)
            scene.backgroundSphere = true;
            ////FOND IMAGE DE LA SCENE FIN////

            ////SOL////
            meshFloor = new THREE.Mesh(
                new THREE.CircleGeometry(1, 50),
                new THREE.MeshStandardMaterial({
                    map: new THREE.TextureLoader().load("textures/sol.jpeg"),
                    roughness: 1.0}));
            meshFloor.rotation.x -= Math.PI / 2;
            meshFloor.receiveShadow = true;
            scene.add(meshFloor);
            ////FIN SOL////

            ////LIGHT////
            let light = new THREE.HemisphereLight(0xffffff, 0x444444);
            light.position.set(0, 10, 0);
            scene.add(light);

            ////AMBIENT LIGHT////
            let ambient = new THREE.AmbientLight(0xffffff, 1.5);
            scene.add(ambient);
            ////AMBIENT LIGHT FIN////

            ////LIGHT 1 LUMIERE ROUGE////
            var SpotLight = new THREE.SpotLight(0xffffff, 3);
            SpotLight.position.set(3, 1, 0);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.shadowCameraVisible = false;
            SpotLight.decay = 1.5;
            SpotLight.penumbra = 1;
            SpotLight.angle = 0.4;

            scene.add(SpotLight);

            // var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0xFF0000 ) ;
            // scene.add( spotLightHelper );

            //// //LIGHT 2 LUMIERE VERTE////
            var SpotLight = new THREE.SpotLight(0xffffff, 5);
            SpotLight.position.set(3, 1,2);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.decay = 1.5;
            SpotLight.penumbra = 1;
            SpotLight.angle = 0.4;

            scene.add(SpotLight);

            // var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0x64FF00 ) ;
            // scene.add( spotLightHelper );

            ////LIGHT 3 LUMIERE BLEU////
            var SpotLight = new THREE.SpotLight(0xffffff, 2);
            SpotLight.position.set(-3, 1, 1);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.decay = 1.5;
            SpotLight.penumbra = 1;
            SpotLight.angle = 0.4;

            scene.add(SpotLight);

            // var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0x00C9FF ) ;
            // scene.add( spotLightHelper );

            ////LIGHT 4 LUMIERE JAUNE////

            // var SpotLight = new THREE.SpotLight(0xffffff, 5);
            // SpotLight.position.set(-5, 5, -15);
            // SpotLight.target.position.set(0, 0, 0);
            // SpotLight.castShadow = true;
            // SpotLight.decay = 1;
            // SpotLight.penumbra = 1;
            // scene.add(SpotLight);

            // var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0xFFFF00 ) ;
            // scene.add( spotLightHelper );

            ////////LIGHT FIN////

            let pmremGenerator = new PMREMGenerator(texture);
            pmremGenerator.update(renderer);


            let pmremCubeUVPacker = new PMREMCubeUVPacker(pmremGenerator.cubeLods);
            pmremCubeUVPacker.update(renderer);

            envMap = pmremCubeUVPacker.CubeUVRenderTarget.texture;

            pmremGenerator.dispose();
            pmremCubeUVPacker.dispose();

            //

            initModel();
            initMaterials();
            initMaterialSelectionMenus();

        });

        ////RENDER ENGINE ////
        renderer = new THREE.WebGLRenderer({antialias: true});
        renderer = new THREE.WebGLRenderer({alpha: true});
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.gammaOutput = true;
        renderer.physicallyCorrectLights = true;
        renderer.gammaInput = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.setClearColor(0x000000, 1);
        renderer.shadowMap.enabled = true;
        container.appendChild(renderer.domElement);
        ////RENDER ENGINE FIN////

        window.addEventListener('resize', onWindowResize, false);

        renderer.setAnimationLoop(function () {
            update();
            render()
        });
    }
    ////INITIALISATION FIN////

    ////INITIALISATION MODEL////
    function initModel() {
        let dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        let loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);

        loader.load('models/gltf/ambrosia/bouteille-ambrosia.gltf', function (gltf) {

            objModel = gltf.scene.children[0];

            objModel.traverse(function (child) {
                if (child.isMesh) {
                    child.material.envMap = envMap;
                }
            });

            scene.add(objModel);

            // car parts for material selection
            modelParts.body.push(objModel.getObjectByName('body'));

            updateMaterials();
        });
    }
    ////INITIALISATION MODEL FIN////

    ////UPDATE MODEL////
    function updateModel(){
        scene.remove(objModel)
        let dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        let loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);
        //liens des objets 3D
        if(bodyModelSelect.value === "Carte de visite"){
            liens = "models/gltf/carte/carte.gltf"
        }else if(bodyModelSelect.value === "E-Liquide"){
            liens = "models/gltf/ambrosia/bouteille-ambrosia.gltf"
        }

        loader.load(liens, function (gltf) {
            objModel = gltf.scene.children[0];

            objModel.traverse(function (child) {
                child.type="Mesh"
                if (child.isMesh) {
                    child.material.envMap = envMap;
                }
            });

            scene.add(objModel);

            // car parts for material selection
            if (objModel.getObjectByName('body')) {
                modelParts.body.push(objModel.getObjectByName('body'));
            }
            updateMaterials();
        });
    }
    ////UPDATE MODEL FIN////


    ////INITIALISATION MATERIALS////
    function initMaterials() {

        materialsLib = {
            main: [
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-belle-prune.jpg"),
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-belle-prune-rgh.jpg"),
                        metalness: 0.0,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Belle Prune'
                    }),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-boree.jpg"),
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-boree-rgh.jpg"),
                        metalness: 0.0,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Borée'
                    }),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-crepe.jpg"),
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-crepe-rgh.jpg"),
                        metalness: 0.0,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Crêpe'
                    }),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-cupcake.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-cupcake-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Cupcake'
                    }),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-euros.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-euros-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Euros'
                    }),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-notos.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-notos-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Notos'
                    }),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-super-beignet.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-super-beignet-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Super Beignet'
                    }),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-zephyr.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-zephyr-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Zephyr'
                    }),
            ],
            Carte : [
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/dorure-or-noir/carte-color.jpg" ),
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-noir/carte-rgh.jpg" ),
                        metalnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-noir/carte-metal.jpg" ),
                        //normalMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-noir/carte-normal.jpg" ) ,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Finition dorée sur fond noir' } ),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/dorure-or-blanc/carte-color-blanc.jpg" ),
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-blanc/carte-rgh-blanc.jpg" ),
                        metalnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-blanc/carte-metal-blanc.jpg" ),
                        //normalMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-blanc/carte-normal-blanc.jpg" ) ,
                        envMap: envMap,
                        envMapIntensity: 0.0,
                        name: 'Finition dorée sur fond blanc' } ),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/dorure-argent-noir/carte-color-argent.jpg" ),
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-argent-noir/carte-rgh-argent.jpg" ),
                        metalnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-argent-noir/carte-metal-argent.jpg" ),
                        //normalMap: new THREE.TextureLoader().load( "cartetextures/dorure-argent-noir/carte-normal-argent.jpg" ),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Finition dorée argent sur fond noir'  } ),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/vernis-selectif/vernis-color.jpg" ),
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/vernis-selectif/vernis-rgh.jpg" ),
                        metalnessMap: 0.0,
                        envMap: envMap,
                        envMapIntensity: 0.4,
                        name: 'Vernis sélectif' } ),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/vernis-3d/vernis-3d-color.jpg" ),
                        metalnessMap: 0.0,
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/vernis-3d/vernis-3d-rgh.jpg" ),
                        //normalMap: new THREE.TextureLoader().load( "cartetextures/vernis-3d/vernis-3d-normal.jpg" ) ,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Vernis sélectif 3D' } ),
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/gauffrage/gauffrage-color.jpg" ),
                        metalnessMap:0.0,
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/gauffrage/gauffrage-rgh.jpg" ),
                        normalMap: new THREE.TextureLoader().load( "cartetextures/gauffrage/gauffrage-normal.jpg" ) ,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Gauffrage' } ),
            ],
        };
    }
    ////INITIALISATION MATERIALS FIN////

    ////INITIALISATION MENU MATERIALS////
    function initMaterialSelectionMenus() {

        function addOption(name, menu) {
            let option = document.createElement('option');
            option.text = name;
            option.value = name;
            menu.add(option);
        }

        if(bodyModelSelect.value === "Carte de visite"){
            bodyMatSelect.innerHTML=""
            affichageGrille()

            materialsLib.Carte.forEach(function (material) {
                addOption(material.name, bodyMatSelect);
            });

            bodyMatSelect.selectedIndex = 0;

        }else if(bodyModelSelect.value === "E-Liquide"){
            bodyMatSelect.innerHTML=""
            affichageGrille()

            materialsLib.main.forEach(function (material) {
                addOption(material.name, bodyMatSelect);
            });
            bodyMatSelect.selectedIndex = 0;
        }
    }
    ////INITIALISATION MENU MATERIALS FIN////

    ////UPDATE MENU MATERIALS////
    function updateMaterials() {
        if(bodyModelSelect.value === "E-Liquide") {
            let bodyMat = materialsLib.main[bodyMatSelect.selectedIndex];

            modelParts.body.forEach(part => part.material = bodyMat);

        }else if (bodyModelSelect.value === "Carte de visite") {
            let bodyMatt = materialsLib.Carte[bodyMatSelect.selectedIndex];
            modelParts.body.forEach(part => part.material = bodyMatt);
        }
    }
    ////UPDATE MENU MATERIALS FIN////

    ////CLICK FILES USDZ////
    function OnOffUsdz(){
        t +=1
        if (t === 1){
        } else {
            t =0
        }
        affichageGrille()
    }
    ////CLICK FILES USDZ FIN////

    ////DISPLAY GRID USDZ////
    function affichageGrille(){
        let src={
            0:'miniatures/belle-prune.jpg',
            1:'miniatures/boree.jpg',
            2:'miniatures/crepe.jpg',
            3:'miniatures/cupcake.jpg',
            4:'miniatures/euros.jpg',
            5:'miniatures/notos.jpg',
            6:'miniatures/super-beignet.jpg',
            7:'miniatures/zephyr.jpg'
        };
        let srcCarte={
            0:'miniatures/FinitionDoreeSurFondNoir.png',
            1:'miniatures/FinitionDoreeSurFondBlanc.png',
            2:'miniatures/FinitionDoreeArgentSurFondNoir.png',
            3:'miniatures/VernisSelectif.png',
            4:'miniatures/VernisSelectif3D.png',
            5:'miniatures/Gauffrage.png'
        };
        grilleSelect.style.display="";
        grilleSelect.innerHTML=null;
        if (t===1){
            if(bodyModelSelect.value === "E-Liquide"){
                for (let i = 0; i < 8; i++) {
                    grilleSelect.innerHTML+= '<a href="usdz/bottle-'+(i+1)+'.usdz">'+'<img class="miniature" src="'+src[i]+'"></a>'
                }
            }else if (bodyModelSelect.value === "Carte de visite"){
                for (let i = 0; i < 6; i++) {
                    grilleSelect.innerHTML+= '<a href="#">'+'<img class="miniature" src="'+srcCarte[i]+'"></a>'
                }
            }
        }else{
            grilleSelect.style.display = "none";
        }
    }
    ////DISPLAY GRID USDZ FIN////

    ////RESIZE WINDOW////
    function onWindowResize() {

        scnCamera.aspect = window.innerWidth / window.innerHeight;
        scnCamera.updateProjectionMatrix();

        renderer.setSize(window.innerWidth, window.innerHeight);
    }
    ////RESIZE WINDOW FIN////

    ////UPDATE////
    function update() {

    }
    ////UPDATE FIN////

    ////CLICK ROTATION 360////
    function OnOffRotation(){
        n +=1;
        if (n === 1){
        } else {
            n =0
        }
        animate()
    }
    ////CLICK ROTATION 360 FIN////

    ////ANIMATION 360////
    function animate() {
        if (n === 1) {
            objModel.rotation.z += 0.01;
            meshFloor.rotation.z -=0.01;
            requestAnimationFrame(animate);
            render()
        } else {
            render()
        }
    }
    ////ANIMATION 360 FIN////

    ////RENDER////
    function render() {
        renderer.render(scene, scnCamera);
    }
    ////RENDER FIN////

    ////CLICK INFO BUTTON////
    function OnOffInfo(){
        i +=1;
        if (i === 1){
        } else {
            i =0
        }
        infoProduit()
    }
    ////CLICK INFO BUTTON FIN////

    ////INFO TEXT BUTTON////
    function infoProduit(){
        if (i === 1) {
            if (bodyModelSelect.value === "E-Liquide"){
                infoTxt.style.display=null;
                infoTxt.innerHTML = "Vous pouvez voir quelques exemples d'étiquettes que nous sommes capables de designer pour vous."
            }else if (bodyModelSelect.value === "Carte de visite"){
                infoTxt.style.display=null;
                infoTxt.innerHTML = "Vous pouvez voir quelques exemples de carte de visite avec plusieurs designs que nous sommes capables de réaliser pour vous "
            }
        } else {
            infoTxt.style.display="none"
        }
    }
    ////INFO TEXT BUTTON FIN////

    ////CLICK MENU////
    function OnOffMenu(){
        x +=1;
        if (x === 1){
        } else {
            x =0
        }
        displayMenu()
    }
    ////CLICK MENU FIN////

    ////DISPLAY MENU////
    function displayMenu() {
        if (x === 1) {
            inter.style.display = "none"
        } else {
            inter.style.display = ""
        }
    }
    ////DISPLAY MENU FIN////

    ////CHECKBOX AFFICHAGE////
    function checkboxAfficher() {
        if (checkboxAffichage.checked){
            grilleSelect.style.display = null;
            info.style.display = null;
            if (infoTxt.style.display !== "none"){
                infoTxt.style.display = null;
            }
            inter.style.display = null;
            menuLogo.style.display = null
        }else {
            grilleSelect.style.display = "none";
            info.style.display = "none";
            infoTxt.style.display = "none";
            inter.style.display = "none";
            menuLogo.style.display = "none"
        }
    }
    ////CHECKBOX AFFICHAGE FIN////

    init();

</script>

</body>
</html>
