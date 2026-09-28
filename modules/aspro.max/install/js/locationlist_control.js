const debounceRequestLocations = BX.debounce(requestLocations, 500);
const selectors = {
  $clearButton: '.location-text__clear',
  $container: '.location-container',
  $input: '.location-text__input',
  $inputValue: '.location-text__hidden',
  $itemsContainer: '.results__table',
  $resultContainer: '.location-results',
};
BX.ready(function() {
  const $containers = document.querySelectorAll('.location-container');

  // observer for dropdown position
  const ob = new IntersectionObserver(function(entries){
    entries.forEach(function(entry) {
      const ratio = entry.intersectionRatio;
      const $target = entry.target;
  
      if (ratio > 0) {
        if (ratio < 1) {
          $target.classList.add('ontop', 'show');
        } else {
          $target.classList.add('show');
        }
      } else {
        $target.classList.remove('ontop', 'show');
      }
    })
  }, {
    root: document.querySelector('.bx-core-adm-dialog') || null
  });
  
  // for multiple props
  for (let i = 0; i < $containers.length; i++) {
    const block = {};
    Object.keys(selectors).forEach(function(key) {
      block[key] = $containers[i].querySelector(selectors[key]);
    });

    // watch input interaction
    block['$input'].addEventListener('input', function() {
      debounceRequestLocations(0, block);
    });
    block['$input'].addEventListener('focus', function() {
      if (!block['$itemsContainer'].querySelector('.location-results__item')) {
        requestLocations(0, block);
      } else {
        block['$resultContainer'].classList.remove('hidden');
      }
      closeLocationsDropdown(block['$resultContainer'])
    });

    // watch dropdown position
    ob.observe(block['$resultContainer']);
  }

  document.addEventListener('click', function(e) {
    const $target = e.target;
    
    // watch clicks outside of block to close
    if (!$target.closest(selectors['$container'])) {
      closeLocationsDropdown();
    }

    // watch close buttons
    if ($target.closest(selectors['$clearButton'])) {
      const $block = $target.closest(selectors['$container']);
      
      $block.querySelector(selectors['$input']).value = $block.querySelector(selectors['$inputValue']).value = $block.querySelector(selectors['$itemsContainer']).innerHTML = '';
      $block.querySelector(selectors['$clearButton']).classList.add('hidden');
    }
  });
})

function closeLocationsDropdown($resultContainer) {
  const $resultContainers = document.querySelectorAll(selectors['$resultContainer']);

  for (let i = 0; i < $resultContainers.length; i++) {
    if ($resultContainers[i] !== $resultContainer) {
      $resultContainers[i].classList.add('hidden');
      $resultContainers[i].classList.remove('ontop', 'show');
    }
  }
}

function requestLocations(page, block) {
  const phrase = block['$input'].value;
  const data = {
    select: {
      CODE: "CODE",
      TYPE_ID: "TYPE_ID",
      VALUE: "ID",
      DISPLAY: "NAME.NAME",
    },
    additionals: ['PATH'],
    filter: {
      "=NAME.LANGUAGE_ID": "ru",
      "=PHRASE": phrase,
    },
    version: '2',
    PAGE_SIZE: 10,
    PAGE: page || 0,
  };

  if (phrase) {
    BX.ajax({
      async: true,
      data: data,
      dataType: "JSON",
      method: "POST",
      url: "/bitrix/components/bitrix/sale.location.selector.search/get.php",
      onsuccess: function (response) {
        if (response.result && response.data.ITEMS.length) {
          if (!page) {
            block['$itemsContainer'].innerHTML = '';
          }

          appendLocationResult(response.data.ITEMS, response.data.ETC, block);
          const ob = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting && entries[0].intersectionRatio) {
              ob.unobserve(entries[0].target);
              requestLocations(page+1, block);
            }
          });
          ob.observe(block['$itemsContainer'].lastElementChild);
          
          if (!page) {
            block['$resultContainer'].classList.remove('hidden');
          }
        } else if(!page) {
          closeLocationsDropdown();
        }
      },
      onfailture: function () {},
    });

    block['$clearButton'].classList.remove('hidden');
  } else {
    closeLocationsDropdown();
    block['$clearButton'].classList.add('hidden');
    block['$inputValue'].value = '';
  }
}

function appendLocationResult(items, info, block) {
  items.forEach(function(item) {
    const itemPath = item.PATH.map(function(path) {
      return info.PATH_ITEMS[path].DISPLAY;
    }).join(', ');

    const $block = BX.create('div', {
      attrs: {
        className: 'adm-list-table-row location-results__item',
      },
      children: [
        BX.create('input', {
          attrs: {
            className: 'adm-designed-radio location-results__item-input',
            id: 'designed_radio_' + item.VALUE,
            type: 'radio',
            value: item.VALUE,
            name: 'location',
          },
        }),
        BX.create('label', {
          attrs: {
            className: 'location-results__item-cell',
            for: 'designed_radio_' + item.VALUE,
          },
          events: {
            click: function() {
              const $item = this.closest('.location-results__item');
              block['$inputValue'].value = $item.querySelector('.location-results__item-input').value;
              block['$input'].value = $item.querySelector('.location-results__item-city-name').innerText;
            
              closeLocationsDropdown();
            }
          },
          children: [
            BX.create('div', {
              children: [
                BX.create('span', {
                  attrs: {
                    className: 'location-results__item-city-name'
                  },
                  html: item.DISPLAY,
                }),
              ]
            }),
            BX.create('div', {
              attrs: {
                className: 'location-results__item--muted',
              },
              text: itemPath,
            }),
          ]
        }),
      ]
    });
    block['$itemsContainer'].appendChild($block);
  });
}