<?php

namespace App\Console\Commands\Mobile;

use App\Console\Commands\Core\Helpers;
use App\Console\Commands\Core\Reader;
use Illuminate\Console\Command;

/**
 * Make render views for Mobile React JS
 *
 * @package App\Console\Commands\Mobile
 * @date 24/04/2020
 * @author Jerome Dh <jdieuhou@gmail.com>
 */
class MakeMobile extends Command
{

    /**
     * Trait des utilitaires
     *
     * Trait Helpers
     */
    use Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mobile:views
                            {--c|classe=all : To generated the seeder for the classes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make views for React Native';

    /**
     * Array of all classes
     *
     * @var array
     */
    protected $tabClasses = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dirname = database_path('migrations');

        try {
            //Retrieve the argument value
            $classe = $this->option('classe');
            $reader = new Reader($dirname);

            if ($classe == 'all') {
                $tabClasses = $reader->getAllClasses();
            } else {
                $tabClasses = $reader->getOnlyClasses([$classe]);
            }

            $this->tabClasses = $reader->getAllClasses();

            $this->process($tabClasses);

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;

    }

    /**
     * Process writing in files
     *
     * @param array $tabClasses
     */
    protected function process(array $tabClasses) {

        $output_dir = resource_path('views/Mobile');
        $this->checkAndCreateTheOutputDir($output_dir);

        foreach ($tabClasses as $name => $datas) {

            //Outputs filenames
            $className = $this->removePlural($name);
            $dir_name = $output_dir.'/'.$name;
            $bottomtabfile = $dir_name.'/BottomTab'.$className.'s.js';
            $confirmationfile = $dir_name.'/ScreenConfirmation'.$className.'.js';
            $createfile = $dir_name.'/ScreenCreate'.$className.'.js';
            $deletefile = $dir_name.'/ScreenDelete'.$className.'.js';
            $indexfile = $dir_name.'/ScreenIndex'.$className.'s.js';
            $itemfile = $dir_name.'/ScreenItemList'.$className.'.js';
            $showfile = $dir_name.'/ScreenShow'.$className.'.js';

            //Checking dirname existence
            if(file_exists($dir_name))
            {
                if ( ! $this->confirm('Dir <<'.$dir_name.'>> alreally exists, do you wan to erase it ?'))
                {
                    $this->info('Successful cancel this command !');
                    continue;
                }
                else {
                    system('rm -r '.$dir_name);
                }
            }

            // Created new empty dir
            mkdir($dir_name);

            $this->info('Attempting ...');

            //Writing files in the folder
            $this->simpleWrite($bottomtabfile, $this->getBottomTabContent($className, $datas));
            $this->simpleWrite($confirmationfile, $this->getScreenConfirmationContent($className, $datas));
            $this->simpleWrite($createfile, $this->getScreenCreateContent($className, $datas));
            $this->simpleWrite($deletefile, $this->getScreenDeleteContent($className, $datas));
            $this->simpleWrite($indexfile, $this->getScreenIndexContent($className, $datas));
            $this->simpleWrite($itemfile, $this->getScreenItemContent($className, $datas));
            $this->simpleWrite($showfile, $this->getScreenShowContent($className, $datas));
        }

        $this->info('Operations finished successfully');

    }

    /**
     * Get the BottomTab content
     *
     * @param string $name
     * @param array $datas
     * @return string
     */
    protected function getBottomTabContent(string $name, array $datas) : string {

        $pluralName = $name.'s';
        $lowerName = strtolower($name);

        return '\'use strict\';

import React from \'react\';
import { Image } from \'react-native\';
import { createStackNavigator } from "@react-navigation/stack";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { IMAGE } from \'../../../constants/Images\';
import { navOptionHandler } from \'../../../constants/Navs\';

import { ScreenIndex'.$pluralName.' } from \'./ScreenIndex'.$pluralName.'\';
import { ScreenConfirmation'.$name.' } from \'./ScreenConfirmation'.$name.'\';
import { ScreenDelete'.$name.' } from \'./ScreenDelete'.$name.'\';
import { ScreenShow'.$name.' } from \'./ScreenShow'.$name.'\';
import { ScreenCreate'.$name.' } from \'./ScreenCreate'.$name.'\';

const BottomTab = createBottomTabNavigator();

// Export content
export default function BottomTab'.$pluralName.'() {

    return (

        <BottomTab.Navigator

            screenOptions={({route}) => ({

                tabBarIcon: ({focused, color, size}) => {
                    let iconName;

                    if(route.name === \''.$pluralName.'\') {
                        iconName = focused
                            ? IMAGE.LIST
                            : IMAGE.LIST;
                    }
                    else if(route.name == \'Nouveau '.$lowerName.'\') {
                        iconName = focused
                            ? IMAGE.USER_PLUS
                            : IMAGE.USER_PLUS;
                    }

                    return <Image source={iconName} style={{width: 20, height: 20,}} resizeMode="contain"/>
                }
            })}

            tabBarOptions ={{
                activeTintColor: \'darkgreen\',
                inactiveTintColor: \'gray\',
            }}

        >
            <BottomTab.Screen name="'.$pluralName.'" component={StackIndex'.$pluralName.'} />
            <BottomTab.Screen name="Nouveau '.$lowerName.'" component={StackCreate'.$pluralName.'} />

        </BottomTab.Navigator>

    )
}

const StackIndex = createStackNavigator();

// Stack nav for index
function StackIndex'.$pluralName.'() {

    return (
        <StackIndex.Navigator initialRouteName="ScreenIndex'.$pluralName.'">
            <StackIndex.Screen name="ScreenIndex'.$pluralName.'" component={ ScreenIndex'.$pluralName.' } options={ navOptionHandler } />
            <StackIndex.Screen name="ScreenDelete'.$name.'" component={ ScreenDelete'.$name.' } options={ navOptionHandler } />
            <StackIndex.Screen name="ScreenShow'.$name.'" component={ ScreenShow'.$name.' } options={ navOptionHandler } />
        </StackIndex.Navigator>
    );

}

const StackCreate = createStackNavigator();

// Stack nav for create
function StackCreate'.$pluralName.'() {

    return (
        <StackCreate.Navigator initialRouteName="ScreenCreate'.$name.'">
            <StackCreate.Screen name="ScreenCreate'.$name.'" component={ ScreenCreate'.$name.' } options={ navOptionHandler } />
            <StackCreate.Screen name="ScreenConfirmation'.$name.'" component={ ScreenConfirmation'.$name.' } options={ navOptionHandler } />
        </StackCreate.Navigator>
    );

}';

    }

    /**
     * Get screen confirmation screen
     *
     * @param string $name
     * @param array $datas
     * @return string
     */
    protected function getScreenConfirmationContent(string $name, array $datas) : string {

        return '\'use strict\';

import React from \'react\';
import { View, Text, SafeAreaView, ScrollView } from \'react-native\';
import { CustumHeaderAdmin } from \'../CustumHeaderAdmin\';
import { AccueilAdminStyles } from \'../../../styles/CommonStyles\';

//Export content
export class ScreenConfirmation'.$name.' extends React.Component {

    render() {

        const { navigation } = this.props;

        return (

            <SafeAreaView style={ styles.root }>

                <CustumHeaderAdmin title="Confirmation" isHome={false} navigation={navigation}/>

                <ScrollView style={ styles.container }>

                    <View  style={ styles.title }>

                        <Text style={ styles.h1 }>Affichage en cours</Text>

                    </View>

                </ScrollView>

            </SafeAreaView>

        );
    }
}

const styles = AccueilAdminStyles();';
    }

    /**
     * Get create content
     *
     * @param string $name
     * @param array $datas
     * @return string
     */
    protected function getScreenCreateContent(string $name, array $datas) : string {

        $pluralName = $name.'s';
        $lowerName = strtolower($name);

    return '\'use strict\';

import React from \'react\';
import { View, Text, TouchableOpacity, ScrollView, SafeAreaView, Alert, Picker } from \'react-native\';
import { CustumHeaderAdmin } from \'../CustumHeaderAdmin\';
import { CommonStyles, inscriptionStyles } from \'../../../styles/CommonStyles\';
import Input from \'../../../components/Input\';
import Submit from \'../../../components/Submit\';
import { BASE_URL_API } from \'../../../constants/urls\';
import { DEFAULT_USER_TYPE } from \'../../../constants/users\';
import { SelectCountry, SelectSexe } from \'../../../components/Select\';

// Export content
export class ScreenCreate'.$name.' extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            success: \'\',
            error: \'\',
            attempting: \'\',
            '.$this->getState1($datas).'
        };

    }

    processSave = async () => {

        // console.log(this.state);

        this.setState({
            success: \'\',
            error: \'\',
            attempting: \'En cours ..\',
        });

        await fetch(BASE_URL_API + \'/'.$lowerName.'s/create\', {
			method: \'POST\',
			headers: {
				\'Accept\': \'application/json\',
				\'Content-Type\': \'application/json\',
			},
			body: JSON.stringify({
                '.$this->getState2($datas).'
            }),

		})
		.then((response) => {
            return response.json();
        })
		.then((responceJson) => {

            if(responceJson[\'statut\'] === 200) {

                // console.log(responceJson[\'response\']);

                let '.$lowerName.' = responceJson[\'response\'][\''.$lowerName.'\'];

                this.setState({
                    success: \''.$lowerName.' enregistre avec succes\',
                    error: \'\',
                    attempting: \'\',

                });

            }
            else if(responceJson[\'statut\'] === 400){

                // console.log(responceJson[\'error\']);

                this.setState({
                    error: "Certaines donnees sont incorrectes, remplissez correctement les champs !",
                    success: \'\',
                    attempting: \'\',
                });

            }
            else {

                // console.log(responceJson[\'error\']);

                this.setState({
                    error: "Erreur interne du serveur, contactez l\'administrateur !",
                    success: \'\',
                    attempting: \'\',
                });

            }

		})
		.catch((error) => {

            // console.warn(error);

            this.setState({
                error: \'Erreur de reseau, veuillez recommencer !\',
                success: \'\',
                attempting: \'\',
            });

		});
    }

    render() {

        const { navigation } = this.props;

        return (

            <SafeAreaView style={ styles.root }>

                <CustumHeaderAdmin title="Nouveau '.$lowerName.'" isHome={ true } navigation={ navigation }/>

                <View style={styles.container}>

                    <ScrollView style={styles.content}  >

                        <Text style={styles.formTitle}>
                            Ajouter un '.$lowerName.'
                        </Text>
                        '.$this->getFieldsForm($datas).'

                        <Submit
                            title="Enregistrer"
                            action={ this.processSave }
                        />

                        <View style={ commons.center }>
                            <Text style={ commons.error }>{ this.state.attempting }</Text>
                            <Text style={ commons.error }>{ this.state.error }</Text>
                            <Text style={ commons.success }>{ this.state.success }</Text>
                        </View>

                        <View style={styles.helpContainer}>
                            <TouchableOpacity onPress={ () => Alert.alert(\'Remplissez les champs tels indiques sur les exemples\') } style={styles.helpLink}>
                                <Text style={styles.helpLinkText}>Besoin d\'aide</Text>
                            </TouchableOpacity>
                        </View>

                    </ScrollView>

                </View>

            </SafeAreaView>

        );
    }
}

const styles = inscriptionStyles();
const commons = CommonStyles();';
    }

    /**
     * Get Screen delete content
     *
     * @param string $name
     * @param array $datas
     * @return string
     */
    protected function getScreenDeleteContent(string $name, array $datas) : string {

        $pluralName = $name.'s';
        $lowerName = strtolower($name);

        return '\'use strict\';

import React from \'react\';
import { View, Text, SafeAreaView, ScrollView } from \'react-native\';
import { CustumHeaderAdmin } from \'../CustumHeaderAdmin\';
import { AccueilAdminStyles } from \'../../../styles/CommonStyles\';

// Export content
export class ScreenDelete'.$name.' extends React.Component {

    render() {

        const { navigation } = this.props;

        return (

            <SafeAreaView style={ styles.root }>

                <CustumHeaderAdmin title="Suppression d\'un '.$lowerName.'" isHome={ false } navigation={ navigation }/>

                <ScrollView contentContainerStyle={ styles.container }>

                    <View  style={ styles.title }>

                        <Text style={ styles.h1 }>Affichage en cours</Text>

                    </View>

                </ScrollView>

            </SafeAreaView>

        );
    }
}

const styles = AccueilAdminStyles();';

    }

    /**
     * Get Screen index content
     *
     * @param string $name
     * @param array $datas
     * @return string
     */
    protected function getScreenIndexContent(string $name, array $datas) : string {

        $pluralName = $name.'s';
        $lowerName = strtolower($name);

        return '\'use strict\';

import React from \'react\';
import { View, Text, SafeAreaView } from \'react-native\';
import { CustumHeaderAdmin } from \'../CustumHeaderAdmin\';
import { CommonStyles, userStyle } from \'../../../styles/CommonStyles\';
import { FlatList } from \'react-native-gesture-handler\';
import ScreenItemList'.$name.' from \'./ScreenItemList'.$name.'\';
import { BASE_URL_API } from \'../../../constants/urls\';

// Export content
export class ScreenIndex'.$pluralName.' extends React.Component {

    state = {
        isLoading: false,
        error: \'Chargement..\',
        '.$lowerName.'s: null,
    };

    async componentDidMount() {

        await fetch(BASE_URL_API + \'/'.$lowerName.'s/getAll\', {

            method: \'POST\',

			headers: {
				\'Accept\': \'application/json\',
				\'Content-Type\': \'application/json\',
			},
			body: JSON.stringify({
				sort: \''.$this->getFirstKeyName($datas).'\',
                order: \'DESC\',
                per: 100,
                page: 1,
			}),

		})
		.then((response) => {
            return response.json();
        })
		.then((responceJson) => {

            if(responceJson[\'statut\'] === 200) {

                let '.$lowerName.' = responceJson[\'response\'];

                this.setState({
                    isLoading: true,
                    '.$lowerName.'s: '.$lowerName.',
                });

            }
            else if(responceJson[\'statut\'] === 400) {

                this.setState({error: "Format de requete incorrect, veuillez recommencer !"});
            }
            else {
                this.setState({error: "Erreur interne du serveur, contactez l\'administrateur !"});
            }

		})
		.catch((error) => {
            // console.warn(error);
            this.setState({error: \'Erreur de reseau, veuillez recommencer !\'})
        });

    }

    render() {

        const { navigation } = this.props;

        return (

            <SafeAreaView style={ styles.root }>

                <CustumHeaderAdmin title="Liste '.$lowerName.'s" isHome={ true } navigation={ navigation } />

                {

                    this.state.isLoading ?
                    (
                        <FlatList
                            data={ this.state.'.$lowerName.'s }
                            renderItem={ ({ item }) => <ScreenItemList'.$name.' '.$lowerName.'={ item } navigation={ navigation } /> }
                            keyExtractor={ (item, index) => index.toString()}
                        />
                    )
                    :
                    (
                        <View style={ [commons.container, commons.padding_small] }>
                            <Text style={ commons.align_center }>{ this.state.error }</Text>
                        </View>
                    )

                }

            </SafeAreaView>

        );
    }
}

const commons = CommonStyles();
const styles = userStyle();';

    }

    /**
     * Get item list content
     *
     * @param string $name
     * @param array $datas
     * @return string
     */
    protected function getScreenItemContent(string $name, array $datas) : string {

        $pluralName = $name.'s';
        $lowerName = strtolower($name);

        $imgFields = array_key_exists('image', $datas) ? $lowerName.'[\'image\']': 'null';

        return '\'use strict\';

import React from \'react\';
import { View, Text, Image } from \'react-native\';
import FadeInAnim from \'../../guest/FadeInAnim\';
import { AssetsObject } from \'../../../constants/users\';
import { TouchableOpacity } from \'react-native-gesture-handler\';
import { CommonStyles, userStyle } from \'../../../styles/CommonStyles\';
import { IMAGE } from \'../../../constants/Images\';

// Export content
export default class ScreenItemList'.$name.' extends React.Component {

    render() {

        const { navigation, '.$lowerName.' } = this.props;

        return (

            <FadeInAnim>

                <TouchableOpacity
                    style={ [styles.line] }

                    onPress={ () => navigation.navigate(\'ScreenShow'.$name.'\', {\''.$lowerName.'\': '.$lowerName.'}) }
                >
                    <View style={ styles.line_col1 }>
                        <AssetsObject width="80" height="60" img={ '.$imgFields.' } />
                    </View>

                    <View style={ styles.line_col2 }>

                        <Text style={ [commons.h3, commons.color_primary] }>
                            { '.$lowerName.'[\''.$this->getFirstKeyName($datas).'\'] }
                        </Text>
                        '.$this->getItemView($lowerName, $datas).'

                    </View>

                </TouchableOpacity>

            </FadeInAnim>
        );
    }
}

const commons = CommonStyles();
const styles = userStyle();';

    }

    protected function getScreenShowContent(string $name, array $datas) : string {

        $pluralName = $name.'s';
        $lowerName = strtolower($name);

        $imgFields = array_key_exists('image', $datas) ? $lowerName.'[\'image\']': 'null';

        return '\'use strict\';

import React from \'react\';
import { View, Text, SafeAreaView, ScrollView, Alert, Image } from \'react-native\';
import { CustumHeaderAdmin } from \'../CustumHeaderAdmin\';
import { CommonStyles, userStyle } from \'../../../styles/CommonStyles\';
import { AssetsObject } from \'../../../constants/users\';
import window from \'../../../constants/Layout\'
import { TouchableOpacity } from \'react-native-gesture-handler\';
import { MyDate } from \'../../../classes/MyDate.class\';
import { IMAGE } from \'../../../constants/Images\';
import { BASE_URL_API, BASE_URL } from \'../../../constants/urls\';


// Export content
export class ScreenShow'.$name.' extends React.Component {

    state = {
        success: \'\',
        error: \'\',
        attempting: \'\',
    };

    /**
     * Deleting an '.$lowerName.' in the db
     */
    processDeleting = async (id) => {

        // console.clear();
        // console.log(\'Deleting the '.$lowerName.' id: \' + id);

        this.setState({
            success: \'\',
            error: \'\',
            attempting: \'En cours ..\',
        });

        await fetch(BASE_URL_API + \'/'.$lowerName.'s/destroy\', {
			method: \'POST\',
			headers: {
				\'Accept\': \'application/json\',
				\'Content-Type\': \'application/json\',
			},
			body: JSON.stringify({
				id: id,
            }),

		})
		.then((response) => {
            return response.json();
        })
		.then((responceJson) => {

            if(responceJson[\'statut\'] === 200) {

                let response = responceJson[\'response\'];
                this.setState({
                    success: response,
                    error: \'\',
                    attempting: \'\',
                });

                //Go to '.$lowerName.'s list
                this.props.navigation.navigate(\'ScreenIndex'.$pluralName.'\');

            }
            else if(responceJson[\'statut\'] === 400) {
                this.setState({error: "'.$name.' introuvable !"});
            }
            else {
                this.setState({error: "Erreur interne du serveur, contactez l\'administrateur !"});
            }

		})
		.catch((error) => {
            // console.warn(error);
            this.setState({error: \'Erreur de reseau, veuillez recommencer !\'})
		});
    }

    render() {

        const { navigation } = this.props;
        const { '.$lowerName.' } = this.props.route.params;
        const h = parseInt(window.window.height / 3);

        return (

            <SafeAreaView style={ styles.root }>

                <CustumHeaderAdmin title={ '.$lowerName.'[\''.$this->getFirstKeyName($datas).'\'] } isHome={ false } navigation={ navigation }/>

                <ScrollView contentContainerStyle={ styles.container }>

                    <View style={styles.header }>

                        <AssetsObject width={ window.window.width } height={ h } img={ '.$imgFields.' } />

                        <Text style={ [commons.h1, commons.white ]}>
                            { '.$lowerName.'[\''.$this->getFirstKeyName($datas).'\'] }
                        </Text>

                    </View>

                    <View style={ styles.content }>
                        '.$this->getDetailItem($lowerName, $datas).'
                        <View style={ [commons.margin_top ]}>

                            <TouchableOpacity
                                onPress={ () => Alert.alert(\'Modifier\')}
                                style={ commons.margin_top_small }
                            >
                                <View style={ styles.details_3 }>

                                    <Image
                                        source={ IMAGE.PENCIL }
                                        style={ styles.icon }
                                    />

                                    <Text style={ [commons.helpLinkText] }> Modifier</Text>

                                </View>
                            </TouchableOpacity>

                            <TouchableOpacity
                                onPress={ () => {
                                    if(confirm(\'Voulez-vous vraiment supprimer cet element ?\')) {

                                        this.processDeleting('.$lowerName.'[\'id\']) }}

                                    }
                                style={ [ commons.margin_top_small ] }
                            >
                                <View style={ styles.details_3 }>

                                    <Image
                                        source={ IMAGE.BIN }
                                        style={ styles.icon }
                                    />

                                    <Text style={ [commons.helpLinkText, commons.color_danger] }> Supprimer</Text>

                                </View>

                            </TouchableOpacity>

                        </View>

                        <View style={ styles.item }>
                            <Text>{ this.state.attempting }</Text>
                            <Text style={ commons.error }>{ this.state.error }</Text>
                            <Text style={ commons.success }>{ this.state.success }</Text>
                        </View>

                    </View>

                </ScrollView>

            </SafeAreaView>

        );
    }
}

const commons = CommonStyles();
const styles = userStyle();';

    }



    /**
     * Get state datas empty
     *
     * @param array $data
     * @return string
     */
    protected function getState1(array $data) : string
    {
        $ch = '';

        foreach($data as $field => $attrs)
        {
            if( ! empty(trim($field)) and $field != 'id') {

                $ch .= '
            '.trim($field).': \'\',';

            }
        }

        return $ch;
    }

    /**
     * Get state datas filled
     *
     * @param array $data
     * @return string
     */
    protected function getState2(array $data) : string
    {
        $ch = '';

        foreach($data as $field => $attrs)
        {
            if( ! empty(trim($field)) and $field != 'id') {

                $ch .= '
                '.trim($field).': this.state.'.trim($field).',';

            }
        }

        return $ch;
    }


    /**
     * Get fields form
     *
     * @param array $data
     * @return string
     */
    protected function getFieldsForm(array $data) : string
    {
        $ch = '';

        foreach($data as $field => $attrs)
        {
            if( ! empty(trim($field)) and $field != 'id') {

                $ch .= '
                        <Input
                            label="'.ucfirst($this->getDisplayName($field)).'"
                            type="name"
                            placeholder="'.ucfirst($this->getDisplayName($field)).'"
                            onChangeText={ ('.$field.') => { this.setState({'.$field.'}) }}
                            secureText={false}
                        />
';
            }
        }

        return $ch;
    }

    /**
     * Get Item to list view
     *
     * @param string $name
     * @param array $datas
     * @return string
     */
    protected  function getItemView(string $name, array $datas) : string {

        $ret = '';

        $i = 0;
        foreach ($datas as $field => $attrs) {

            if($i == 0 or $i == 1) {
                ++$i;
                continue;
            }
            elseif ($i > 3){
                break;
            }

            if( ! empty(trim($field)) and $field != 'id') {

                $ret .= '
                        <View style={styles.details}>
                            <Image
                                source={ IMAGE.TICKET }
                                style={ styles.icon }
                            />

                            <Text style={ styles.sub_item }>
                                { '.$name.'[\''.$field.'\'] }
                            </Text>
                        </View>
                        ';
            }

            ++$i;

        }

        return $ret;
    }

    /**
     * Get item to print detail
     *
     * @param string $name
     * @param array $datas
     * @return string
     */
    protected function  getDetailItem(string $name, array $datas) : string {

        $ret = '';

        foreach ($datas as $field => $attrs) {

            if (!empty(trim($field)) and $field != 'id' and $field != 'image') {

                $ret .= '
                        <View style={styles.details_2 }>
                            <Image
                                source={ IMAGE.TICKET }
                                style={ styles.icon }
                            />

                            <Text style={ styles.sub_item_2 }>'.$this->getDisplayName(ucfirst($field)).'</Text>

                            <Text style={ [styles.sub_item_3] }>
                                { '.$name.'[\''.$field.'\'] }
                            </Text>
                        </View>
';
                }
        }

        return $ret;

    }


}
