<template>
  <div class="mt-4 mb-2 mb-xl-5">
    <div class="row">
      <div class="col-12 col-lg-5 col-xl-3">
        <div class="card mb-3">
          <div class="card-header">
            <strong v-text="data.mission.name ? data.mission.name : 'Brak nazwy'"></strong>
          </div>
          <p class="m-0 p-2 text-center border-bottom" v-text="data.mission.description" v-if="data.mission.description"></p>
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="data.mission.map">
              Mapa <span class="badge badge-secondary" v-text="data.mission.map"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="data.mission.author">
              Autor <span class="badge badge-secondary" v-text="data.mission.author"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="data.mission.date">
              Data <span class="badge badge-secondary" v-text="data.mission.date"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="data.mission.time">
              Czas <span class="badge badge-secondary" v-text="data.mission.time"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="data.mission.slotCount !== undefined">
              Sloty <span class="badge badge-secondary" v-text="data.mission.slotCount"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="data.mission.resistance">
              Lokalsi
              <span>
                <span class="badge mr-1" v-bind:class="[data.mission.resistance.west ? 'badge-success' : 'badge-danger']">BLUFOR</span>
                <span class="badge" v-bind:class="[data.mission.resistance.east ? 'badge-success' : 'badge-danger']">OPFOR</span>
              </span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="data.mission.headlessPresent !== undefined">
              Headless
              <i class="fa fa-check text-success" v-if="data.mission.headlessPresent"></i>
              <i class="fa fa-times text-danger" v-else></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="data.mission.curatorPresent !== undefined">
              Zeus
              <i class="fa fa-check text-success" v-if="data.mission.curatorPresent"></i>
              <i class="fa fa-times text-danger" v-else></i>
            </li>
          </ul>
        </div>
        <div class="card mb-3" v-if="hasWeather.start || hasWeather.forecast">
          <div class="card-header">
            Pogoda
          </div>
          <template v-if="hasWeather.start">
            <h6 class="border-bottom p-2 text-center">Aktualna</h6>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center"
                  v-for="paramKey in weatherParamsOrder" v-if="data.mission.weather.start[paramKey] !== undefined">
                {{ weatherParamsNames[paramKey] }}
                <span class="badge badge-secondary">
                  {{ getWeatherReadableValue(data.mission.weather.start[paramKey]) }}
                </span>
              </li>
            </ul>
          </template>
          <template v-if="hasWeather.forecast">
            <h6 class="border-bottom p-2 text-center">
              Prognoza<template v-if="weatherTimeOfChanges"> na {{ weatherTimeOfChanges }}</template>
            </h6>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center"
                  v-for="paramKey in weatherParamsOrder" v-if="data.mission.weather.forecast[paramKey] !== undefined">
                {{ weatherParamsNames[paramKey] }}
                <span class="badge badge-secondary">
                  {{ getWeatherReadableValue(data.mission.weather.forecast[paramKey]) }}
                </span>
              </li>
            </ul>
          </template>
        </div>
        <div class="card mb-3" v-if="data.mission.stats && Object.keys(data.mission.stats)">
          <div class="card-header">
            Statystyki
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center"
                v-for="statsKey in statsKeysOrder" v-if="data.mission.stats[statsKey] !== undefined">
              {{ statsKeysNames[statsKey] }}
              <span class="badge badge-secondary" v-text="data.mission.stats[statsKey]"></span>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-12 col-lg-7 col-xl-6">
        <template v-if="sides">
          <template v-for="sideKey in sidesOrder" v-if="sides[sideKey]">
            <h5 class="border-bottom mb-3" v-text="sides[sideKey].name"></h5>
            <div v-for="group in sides[sideKey].groups">
              <div class="card mb-3">
                <div class="card-header" v-text="group.name ? group.name : 'Grupa '+sides[sideKey].name" v-if="sideKey != 'virtual'"></div>
                <ul class="list-group list-group-flush">
									<li class="list-group-item" v-for="unit in group.units">
										<div class="d-flex w-100 justify-content-between">
											<h6 class="mb-1">
                        {{ unit.description ? unit.description : '---' }}
                        <i class="fa fa-bolt" title="Zeus" v-if="unit.curator"></i>
                      </h6>
									    <small class="text-muted" v-text="unit.class" v-if="unit.class"></small>
									  </div>
										<small class="text-muted" v-if="unit.primaryWeapon">Broń: {{ unit.primaryWeapon }}<br /></small>
                    <small class="text-muted" v-if="unit.vehicle">Pojazd: {{ unit.vehicle }}<br /></small>
									  <small class="text-muted" v-if="unit.position">Pozycja: {{ unit.position }}</small>
									</li>
							  </ul>
              </div>
            </div>
          </template>
        </template>
        <div v-else>Brak slotów</div>
      </div>
      <div class="col-12 col-xl-3">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span style="max-width: 75%" v-text="data.pbo.name"></span>
            <span class="badge badge-secondary" v-text="data.pbo.size"></span>
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center"
                v-for="file in data.pbo.files" :key="file.path">
                {{ file.path }} <span class="badge badge-secondary" v-text="file.size"></span>
            </li>
          </ul>
        </div>
        <div class="alert alert-light text-center" style="font-size: 12px;">
          Przetworzono w {{ data.parsingTime }} ms używając {{ data.memoryPeakUsage }} pamięci
          <br /><br />
          <a :href="'?mission='+data.fileHash" target="_blank" class="btn btn-secondary mr-1" style="font-size: 10px;" v-if="data.fileHash">
            <i class="fa fa-floppy-o"></i> Zapisz link
          </a>
          <button type="button" class="btn btn-primary mr-1" style="font-size: 10px;" @click="reset">
            <i class="fa fa-repeat"></i> Wrzuć inną misję
          </button>
          <a href="https://arma3coop.pl/viewtopic.php?f=33&t=11776" target="_blank" class="btn btn-danger" style="font-size: 10px;">
            <i class="fa fa-bug"></i> Zgłoś błąd
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

const sideNames = {
  west: 'BLUFOR',
  east: 'OPFOR',
  independent: 'GREENFOR',
  civilian: 'CIV',
  virtual: 'VIRTUAL'
};

export default {
  name: 'DataViewer',
  data: () => ({
    sidesOrder: ['west', 'east', 'independent', 'civilian', 'virtual'],
    weatherParamsOrder: ['weather','wind','gusts','rain','lightnings','fog','fogDecay','waves'],
    weatherParamsNames: {
      weather: 'Zachmurzenie',
      wind: 'Wiatr',
      gusts: 'Podmuchy',
      rain: 'Deszcz',
      lightnings: 'Pioruny',
      fog: 'Gęstość mgły',
      fogDecay: 'Rozpad mgły',
      waves: 'Wysokość fal'
    },
    statsKeysOrder: ['units', 'groups', 'waypoints', 'triggers', 'markers', 'objects', 'simpleObjects', 'aiGenerators',
    'aiGeneratorsUnits', 'attackGenerators', 'otherModules'],
    statsKeysNames: {
      'units': 'Jednostki',
      'groups': 'Grupy',
      'waypoints': 'Waypointy',
      'triggers': 'Triggery',
      'markers': 'Markery',
      'objects': 'Obiekty (symulowane)',
      'simpleObjects': 'Obiekty (proste)',
      'aiGenerators': 'Generatory AI',
      'aiGeneratorsUnits': 'Jednostki w generatorach AI',
      'attackGenerators': 'Generatory ataków',
      'otherModules': 'Inne moduły'
    }
  }),
  props: {
    data: Object
  },
  computed: {
    sides() {
      const data = this.data;
      const sides = {};

      const groups = data.mission.groups;
      if (groups && groups.length) {
        groups.forEach(group => {
          if (!group.side) return;
          if (!sides[group.side]) sides[group.side] = {
            name: sideNames[group.side],
            groups: []
          };

          sides[group.side].groups.push(group);
        });
      };

      const virtualUnits = data.mission.virtualUnits;
      if (virtualUnits && virtualUnits.length) {
        sides['virtual'] = {
          name: sideNames['virtual'],
          groups: [{units: virtualUnits}]
        };
      };

      if (!Object.keys(sides).length) return null;
      return sides;
    },
    hasWeather() {
      const data = this.data;
      const weather = (data && data.mission) ? data.mission.weather : undefined;

      return {
        start: !!(weather && weather.start && Object.keys(weather.start).length),
        forecast: !!(weather && weather.forecast && Object.keys(weather.forecast).length),
      }
    },
    weatherTimeOfChanges() {
      const data = this.data;
      if (!data || !data.mission) return '';
      const weather = data.mission.weather;
      if (!weather || !weather.timeOfChanges) return '';

      const timeDesc = ['godzin','minut','sekund'];
      return weather.timeOfChanges.split(':').map((value, index) => {
        if (parseInt(value)) return value + ' ' + timeDesc[index];
      }).filter(value => !!value).join(' ');
    }
  },
  methods: {
    reset() {
      this.$emit('reset');
    },
    getWeatherReadableValue(value) {
      return (parseFloat(value)*100).toFixed(2) + '%';
    }
  }
}
</script>

<style scoped>

</style>
