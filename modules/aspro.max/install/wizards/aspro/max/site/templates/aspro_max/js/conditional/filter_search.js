window.appAspro = window.appAspro || {};

if (!window.appAspro.filterSearch) {
  window.appAspro.filterSearch = {
    filterIndex: new Map(),

    mobileExpand: function (container, hide = false){
      if(container){
        const filterSearch = container.querySelector('.filter-search');
        if(filterSearch){
          hide ? filterSearch.classList.add('filter-search--mobile-hide') : filterSearch.classList.remove('filter-search--mobile-hide');
        }
      }
    },

    buildIndexForBlock: function (block) {
      const propertyId = block.dataset.property_id;
      if (!propertyId || this.filterIndex.has(propertyId)) return;

      const blockIndex = new Map();
      const labelBlocks = block.querySelectorAll('.filter-value-wrap');

      labelBlocks.forEach(el => {
        const labelEl = el.querySelector('.filter-value-text');
        const titleRaw = labelEl?.getAttribute('title') || labelEl?.textContent || '';
        const title = titleRaw.toLowerCase().trim();
        if (title) {
          blockIndex.set(title, el);
        }
      });

      this.filterIndex.set(propertyId, blockIndex);
    },

    clearAll: function () {
      this.filterIndex.clear();
    },

    handleInput: function (inputEl) {
      const container = inputEl.closest('.bx_filter_block');
      const block = inputEl.closest('.bx_filter_parameters_box');
      const propertyId = block?.dataset.property_id;
      if (!propertyId || !block) return;

      this.buildIndexForBlock(block);
      const index = this.filterIndex.get(propertyId);

      const query = inputEl.value.toLowerCase().trim();
      const labels = container.querySelectorAll('.filter-value-wrap');

      // Hide all labels
      labels.forEach(el => el.classList.add('hidden'));
      const mobileExpandButton = container.querySelector('.expand_block');

      if (!query) {
        // Show all labels if input is empty
        labels.forEach(el => el.classList.remove('hidden'));

        //show mobile expand
        if(mobileExpandButton){
          mobileExpandButton.classList.remove('hidden');
        }
      } else {
        // Show only matched labels
        index.forEach((el, title) => {
          if (title.includes(query)) {
            el.classList.remove('hidden');
          }
        });

        //hide mobile expand
        if(!mobileExpandButton.classList.contains('expand_block--opened')){
          mobileExpandButton.click();
        }

        if(mobileExpandButton){
          mobileExpandButton.classList.add('hidden');
        }
      }

      // Show or hide "no results" label
      const emptyTitle = block.querySelector('.filter-search-empty-title');
      const hasVisible = Array.from(labels).some(el => !el.classList.contains('hidden'));

      if (emptyTitle) {
        if (hasVisible) {
          emptyTitle.classList.add('hidden');
        } else {
          emptyTitle.classList.remove('hidden');
        }
      }

    },

    init: function () {
      const self = this;

      // Wrap input handler with debounce (using existing global debounce)
      const debouncedInputHandler = debounce(function (inputEl) {
        self.handleInput(inputEl);
      }, 150); // Adjust delay as needed

      // Attach input listener to filter search fields
      document.addEventListener('input', (e) => {
        if (e.target && e.target.classList.contains('filter-search-values')) {
          debouncedInputHandler(e.target);
        }
      });

      // Clear button click behavior
      document.addEventListener('click', (e) => {
        if (e.target.closest('.filter-search__close')) {
          const btn = e.target.closest('.filter-search__close');
          const wrapper = btn.closest('.filter-search');
          const input = wrapper.querySelector('.filter-search-values');

          input.value = '';
          input.focus();

          // trigger filter reset
          if (window.appAspro?.filterSearch?.handleInput) {
            window.appAspro.filterSearch.handleInput(input);
          }
        }
      });

      // Clear index after AJAX updates (e.g., when filter reloads via BX.ajax)
      BX.addCustomEvent('onAjaxSuccess', function () {
        self.clearAll();
      });
      BX.addCustomEvent('onCompleteAction', function () {
        self.clearAll();
      });
    }
  };
}

BX.Aspro.Utils.readyDOM(() => {
  appAspro.filterSearch.init();
});
