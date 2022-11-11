/*
 * EventBus
 *
 * This allows internal app communication, following an emit/on pattern
 * and is used to trigger alerts and pass other information not kept in
 * state to components
 *
 */

import Vue from 'vue';

export const EventBus = new Vue();
