import BaseEvent from './base.event';

export class DragEnterContainerEvent extends BaseEvent
{
	data: {
		clientX: number,
		clientY: number,
		source: HTMLElement,
		sourceContainer: HTMLElement,
		originalSource: HTMLElement,
		enter: HTMLElement,
	};
}
