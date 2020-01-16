<!DOCTYPE html>
<html lang="en">
<head>
    <title>Configurateur</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link type="text/css" rel="stylesheet" href="main.scss">
    <link rel="icon" type="image/jpg" href="textures/agarta.jpg" />
</head>

<body>
<div class="parent">

    <div id="info">
        <a id="checkbox"><input id="checkboxInput" type="checkbox" name=checkbox checked> Affichage</a>
        <a><img id="menu-logo" src="miniatures/hamburger.png"></a>
        <a><img id="info-image" src="miniatures/info.png"></a>
        <a style="display: none" id="info-txt"></a>

        <div id="inter" class="inter" style="width :22rem;">

            <br>

        </div>
            <div class="grille" id="grille"></div>

<!--        <a id="auto-rotate"><img id="img360" src="miniatures/rotate.png"></a>-->
    </div>
</div>

<div id="container"></div>

<div id="sidebar">
    <div id="content-sidebar">
        <div id="new-ui">
            <div id="header-ui">
                <span>Choisir un produit : </span>
                <i> ></i>
            </div>
            <div id="content-ui">
                <ul id="ul-list">
                    <li id="eliquide">E-Liquide</li>
                    <li id="carte1">Carte de Visite</li>
                </ul>
            </div>
        </div>
        <div id="new-ui">
            <div id="header-ui1">
                <span>Choisir un design : </span>
                <i> ></i>
            </div>
            <div id="content-ui1">
                <span><select class="select-css" id="body-mat"></select></span>
            </div>
        </div>
        <div id="new-ui">
            <div id="header-ui2">
                <span>Background : </span>
                <i> ></i>
            </div>
            <div id="content-ui2">
                <ul id="ul-list">
                    <li id="fondGris">Fond gris</li>
                    <li id="fondNature">Fond Nature</li>
                </ul>
            </div>
        </div>
        <div id="new-ui">
            <div id="header-ui3">
                <span>Sol : </span>
                <i> ></i>
            </div>
            <div id="content-ui3">
                <span>Texture : <input type="file"></span>
                <br>
                <span>Palette de couleurs: <br><input type="color"></span>

            </div>
        </div>
        <div id="new-ui">
            <div id="header-ui4">
                <span>Options : </span>
                <i> ></i>
            </div>
            <div id="content-ui4">
                <ul id="ul-list">
                    <li id="auto-rotate">Rotation 360°</li>
                    <li><button id="button-usdz">Fichiers Usdz</button></li>
                </ul>
            </div>
        </div>
    </div>
</div>

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
    let scnCamera, scene, mesh, meshFloor, renderer, objModel, materialsLib, envMap, controls;

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
    let sidebare = document.getElementById('sidebar');
    let headerUi = document.getElementById('header-ui');
    let headerUi1 = document.getElementById('header-ui1');
    let headerUi2 = document.getElementById('header-ui2');
    let headerUi3 = document.getElementById('header-ui3');
    let headerUi4 = document.getElementById('header-ui4');
    let contentUi = document.getElementById('content-ui');
    let contentUi1 = document.getElementById('content-ui1');
    let contentUi2 = document.getElementById('content-ui2');
    let contentUi3 = document.getElementById('content-ui3');
    let contentUi4 = document.getElementById('content-ui4');
    let eLiquide = document.getElementById('eliquide');
    let carte = document.getElementById('carte1');
    let eliquide = document.getElementById('eliquide');
    let fondGris = document.getElementById('fondGris');
    let fondNature = document.getElementById('fondNature');


    let liens;

    let n,i,t,x,a,b,c,d,e = 0;
    let fond = 0;

    let objetActuel = "E-Liquide";

    let modelParts = {
        body: [],
    };

    let scnCameraTarget = new THREE.Vector3();
    ////INITIALISATION DES VARIABLES FIN////

    ////DEBUT addEventListener() List////
    carte.addEventListener('click', updateModelCarte);
    eliquide.addEventListener('click',updateModelELiquide );
    bodyMatSelect.addEventListener('change', updateMaterials);
    rotate.addEventListener("click",OnOffRotation);
    info.addEventListener('click',OnOffInfo);
    buttonUsdz.addEventListener('click',OnOffUsdz);
    menuLogo.addEventListener('click',OnOffMenu);
    checkboxAffichage.addEventListener('change',checkboxAfficher);
    headerUi.addEventListener('click',OnOffHeaderUi);
    headerUi1.addEventListener('click',OnOffHeaderUi1);
    headerUi2.addEventListener('click',OnOffHeaderUi2);
    headerUi3.addEventListener('click',OnOffHeaderUi3);
    headerUi4.addEventListener('click',OnOffHeaderUi4);
    fondGris.addEventListener("click", function () {
        fond=0;
        changerFond()
    })
    fondNature.addEventListener('click',function () {
        fond =1;
        changerFond()
    })
    ////FIN addEventListener() List////

    ////INITIALISATION////
    function init() {

        let container = document.getElementById('container');

        scene = new THREE.Scene();

        scnCamera = new THREE.PerspectiveCamera(40, window.innerWidth / window.innerHeight, 0.001, 100);
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
            controls.maxDistance = 4;
            controls.enableDamping = true;
            controls.dampingFactor = 0.2;
            controls.minPolarAngle = 0.1; //Uper
            controls.maxPolarAngle = 1.8; //Lowers
            controls.target.set(0, 0.8, 0);
            controls.autoRotate = true;
            controls.autoRotateSpeed = 0.05;
            controls.update();

            ////ORBIT CONTROLS FIN////

            //SKYBOX////
            scene = new THREE.Scene();
                scene.background = new THREE.TextureLoader().load("textures/gris.jpg"); // (Ciel)
                scene.backgroundSphere = true;

            ////SKYBOX FIN////

            ////SOL////
            meshFloor = new THREE.Mesh(
                new THREE.CircleGeometry(1, 50),
                new THREE.MeshStandardMaterial({
                    map: new THREE.TextureLoader().load("textures/marbre.jpg"),
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

    ////CHANGER BACKGROUND////
    function changerFond(){
        if (fond === 0){
            scene.remove(mesh)
            scene.background = new THREE.TextureLoader().load("textures/gris.jpg"); // (Ciel)
            scene.backgroundSphere = true;
        }else if (fond === 1){
            let path = "textures/ocean/";
            let format = '.jpg';
            let urls = [
                path + 'right' + format,
                path + 'left' + format,
                path + 'top' + format,
                path + 'bottom' + format,
                path + 'back' + format,
                path + 'front' + format
            ];

            let reflectionCube = THREE.ImageUtils.loadTextureCube(urls);
            reflectionCube.format = THREE.RGBFormat;

            let shader = THREE.ShaderLib[ "cube" ];
            shader.uniforms[ "tCube" ].value = reflectionCube;

            let material = new THREE.ShaderMaterial( {
                fragmentShader: shader.fragmentShader,
                vertexShader: shader.vertexShader,
                uniforms: shader.uniforms,
                depthWrite: false,
                side: THREE.BackSide
            });

            mesh = new THREE.Mesh(new THREE.BoxGeometry(100, 100, 100), material);
            scene.add(mesh);
        }
    }
    ////CHANGER BACKGROUND FIN////

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

    ////UPDATE MODEL LIQUIDE////
    function updateModelELiquide(){
        objetActuel = "E-Liquide";
        scene.remove(objModel);
        let dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        let loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);

            liens = "models/gltf/ambrosia/bouteille-ambrosia.gltf"


        loader.load(liens, function (gltf) {
            objModel = gltf.scene.children[0];

            objModel.traverse(function (child) {
                child.type="Mesh";
                if (child.isMesh) {
                    child.material.envMap = envMap;
                }
            });

            scene.add(objModel);

            // car parts for material selection
            if (objModel.getObjectByName('body')) {
                modelParts.body.push(objModel.getObjectByName('body'));
            }
            initMaterialSelectionMenus()
            updateMaterials();

        });
    }
    ////UPDATE MODEL E-LIQUIDE FIN////

    ////UPDATE MODEL CARTE////
    function updateModelCarte(){
        objetActuel = "Carte de visite";
        scene.remove(objModel);
        let dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        let loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);

            liens = "models/gltf/carte/carte.gltf"

        loader.load(liens, function (gltf) {
            objModel = gltf.scene.children[0];

            objModel.traverse(function (child) {
                child.type="Mesh";
                if (child.isMesh) {
                    child.material.envMap = envMap;
                }
            });

            scene.add(objModel);

            // car parts for material selection
            if (objModel.getObjectByName('body')) {
                modelParts.body.push(objModel.getObjectByName('body'));
            }
            initMaterialSelectionMenus()
            updateMaterials();

        });
    }
    ////UPDATE MODEL CARTE FIN////

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
        if (objetActuel === "E-Liquide"){
            bodyMatSelect.innerHTML="";
            affichageGrille();

            materialsLib.main.forEach(function (material) {
                addOption(material.name, bodyMatSelect);
            });
        }else if (objetActuel === "Carte de visite"){
            bodyMatSelect.innerHTML="";
            affichageGrille();

            materialsLib.Carte.forEach(function (material) {
                addOption(material.name, bodyMatSelect);
            });
        }
            bodyMatSelect.selectedIndex = 0;
    }
    ////INITIALISATION MENU MATERIALS FIN////

    ////UPDATE MENU MATERIALS////
    function updateMaterials() {
        if(objetActuel === "E-Liquide") {
            let bodyMat = materialsLib.main[bodyMatSelect.selectedIndex];

            modelParts.body.forEach(part => part.material = bodyMat);
        }else if (objetActuel === "Carte de visite") {
            let bodyMatt = materialsLib.Carte[bodyMatSelect.selectedIndex];
            modelParts.body.forEach(part => part.material = bodyMatt);
        }
    }
    ////UPDATE MENU MATERIALS FIN////

    ////CLICK FILES USDZ////
    function OnOffUsdz(){
        t +=1;
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
        if (t===0){
            if(objetActuel === "E-Liquide"){
                for (let i = 0; i < 8; i++) {
                    grilleSelect.innerHTML+= '<a href="usdz/bottle-'+(i+1)+'.usdz">'+'<img class="miniature" src="'+src[i]+'"></a>'
                }
            }else if (objetActuel === "Carte de visite"){
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
        if (n === 0) {
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
        i += 1;
        if (i === 1){
        } else {
            i = 0;
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
        if (x === 0) {
            inter.style.display = "none"
            sidebare.style.display = "none"
        } else {
            // inter.style.display = ""
            sidebare.style.display = ""
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

    ////OnOff HEADER UI////
    function OnOffHeaderUi(){
        a +=1;
        if (a === 1){
        } else {
            a =0
        }
        displayUi()
    }
    ////OnOff HEADER UI FIN////

    function OnOffHeaderUi1(){
        b +=1;
        if (b === 1){
        } else {
            b =0
        }
        displayUi1()
    }


    function OnOffHeaderUi2(){
        c +=1;
        if (c === 1){
        } else {
            c =0
        }
        displayUi2()
    }

    function OnOffHeaderUi3(){
        d +=1;
        if (d === 1){
        } else {
            d =0
        }
        displayUi3()
    }

    function OnOffHeaderUi4(){
        e +=1;
        if (e === 1){
        } else {
            e =0
        }
        displayUi4()
    }

    ////DISPLAY UI////
    function displayUi(){
        if (a ===1){
            contentUi.style.display = "none";
        }else{
            contentUi.style.display = "";
        }
    }
    ////DISPLAY UI FIN////

    ////DISPLAY UI 1////
    function displayUi1(){
        if (b ===1){
            contentUi1.style.display = "none";
        }else{
            contentUi1.style.display = "";
        }
    }
    ////DISPLAY UI FIN////

    ////DISPLAY UI 2////
    function displayUi2(){
        if (c ===1){
            contentUi2.style.display = "none";
        }else{
            contentUi2.style.display = "";
        }
    }
    ////DISPLAY UI FIN////

    ////DISPLAY UI 3////
    function displayUi3(){
        if (d ===1){
            contentUi3.style.display = "none";
        }else{
            contentUi3.style.display = "";
        }
    }
    ////DISPLAY UI FIN////

    ////DISPLAY UI 3////
    function displayUi4(){
        if (e ===1){
            contentUi4.style.display = "none";
        }else{
            contentUi4.style.display = "";
        }
    }
    ////DISPLAY UI FIN////

    init();

</script>

</body>
</html>
